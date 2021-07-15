<?php

namespace App\Controller\Dash;

use App\Entity\Attachment;
use App\Entity\ConfigValue;
use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Entity\SocProduct;
use App\Entity\TranslatedDocument;
use App\Form\ConfigValueType;
use App\Form\ProductType;
use App\Repository\ConfigValueRepository;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\SocProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dash/config", name="dash_config_")
 */
class ConfigController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(ConfigValueRepository $valueRepository): Response
    {
        $configValues = $valueRepository->findAll();
        $configValuesForm = $this->createForm(
            ConfigValueType::class,
            new ConfigValue(),
            ['action' => $this->generateUrl('dash_config_new')]
        );

        $migrationTools = [
            [
                'name' => 'Migrate Product Category',
                'description' => 'Convert the old parent products into categories format' ,
                'path' => 'dash_config_product_category_migrate'
            ],
            [
                'name' => 'Migrate Products',
                'description' => 'Convert the old children products to new format' ,
                'path' => 'dash_config_product_migrate'
            ],
        ];
        return $this->render('dash/config/index.html.twig', [
            'configValues' => $configValues,
            'configValuesForm' => $configValuesForm->createView(),
            'migrationTools' => $migrationTools
        ]);
    }

    /**
     * @Route("/toggle/{id}", name="value_toggle", methods={"GET"})
     */
    public function toggleConfigValue(ConfigValue $configValue): RedirectResponse
    {
        $configValue->toggleActive();
        $this->getDoctrine()->getManager()->persist($configValue);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('dash_config_index');
    }


    /**
     * @Route("/new", name="new", methods={"POST"})
     */
    public function new(Request $request): Response
    {
        $configValue = new ConfigValue();
        $form = $this->createForm(ConfigValueType::class, $configValue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($configValue);
            $entityManager->flush();
        }

        return $this->redirectToRoute('dash_config_index');
    }


    /**
     * @Route("/{id}/edit", name="edit", methods={"POST"})
     */
    public function edit(Request $request, ConfigValue $configValue): Response
    {
        $form = $this->createForm(ConfigValueType::class, $configValue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('dash_config_index');
    }


    /**
     * @Route("/migration/tool/product_category", name="product_category_migrate", methods={"GET"})
     */
    public function productCategoryMigrate(
        SocProductRepository $socProductRepository,
        ProductCategoryRepository $categoryRepository
    ): Response {
        $categoryRaw = $socProductRepository->findBy(['parent' => null]);

        $countMigration = 0;
        foreach ($categoryRaw as $item) {
            $category = $categoryRepository->findOneBy(['name' => $item->getReferenceName()]);
            if (!($category instanceof ProductCategory)) {
                $category = new ProductCategory();
                $category->setName($item->getReferenceName())
                    ->setPriority($item->getImportance())
                    ->setActive($item->isEnabled())
                    ->setImageName($item->getImage()->getImageName());

                $category->__set('useDefaultLanguage', $item->isEnglishGlobalTranslation());
                $category->__set('title', [
                        'en' => $item->getTranslatedNameOrNull('en'),
                        'de' => $item->getTranslatedNameOrNull('de'),
                    ]);
                $category->__set('description', [
                        'en' => $item->getTranslatedDescOrNull('en'),
                        'de' => $item->getTranslatedDescOrNull('de'),
                    ]);

                $this->getDoctrine()->getManager()->persist($category);
                $countMigration++;
            }
        }

        try {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('info', "Created $countMigration categories from previous schema");
        } catch (\Exception $exception) {
            $this->addFlash('danger', $exception->getMessage());
        }



        return $this->redirectToRoute('dash_product_index');
    }

    /**
     * @Route("/migration/tool/product", name="product_migrate", methods={"GET"})
     */
    public function productMigrate(
        SocProductRepository $socProductRepository,
        ProductRepository $productRepository,
        ProductCategoryRepository $categoryRepository
    ): Response {
        $oldProductsBag = $socProductRepository->findAll();

        $categories = $categoryRepository->findAll();
        $newProducts = [];

        foreach ($oldProductsBag as $element) {
            if ($element->getParent() === null) {
                continue;
            }


            $product = new Product();

            $product->setName($element->getReferenceName());
            $product->setActive($element->isEnabled());
            $product->setPriority($element->getImportance());
            $product->__set('title', [
                'en' => $element->getTranslatedNameOrNull('en'),
                'de' => $element->getTranslatedNameOrNull('de'),
            ]);
            $product->__set('description', [
                'en' => $element->getTranslatedDescOrNull('en'),
                'de' => $element->getTranslatedDescOrNull('de'),
            ]);
            $product->__set('useDefaultLanguage', $element->isEnglishGlobalTranslation());
            $product->__set('ref', ['id' => $element->getId()]);
            $product->setCreatedAt(\DateTime::createFromInterface($element->getCreatedAt()));
            $product->setUpdatedAt(\DateTime::createFromInterface($element->getUpdatedAt()));
            foreach ($element->getDocumentCollectionInclusive() as $document) {
                /**
                 * @var TranslatedDocument $document
                 */
                foreach ($document->getDocumentsLangs() as $lang) {
                        /**
                         * @var TranslatedDocument $doc
                         */
                        $file = $document->getTranslation($lang);
                        $attach = new Attachment();
                        $attach->setFileName($file->getFileName());
                        $attach->__set('title', $file->getTitle());
                        $attach->__set('lang', $lang);
                        $attach->__set('multilingual', $document->isEnglishGlobalTranslation());
                        $product->addAttachmentCollection($attach);
                }
            }

            $currentCategory = null;

            $parent = $element->getParent();
            while ($parent->getParent() !== null) {
                $parent = $parent->getParent();
            }
            foreach ($categories as $category) {
                /**
                 * @var ProductCategory $category
                 * @var SocProduct $element
                 */
                if ($category->getName() === $parent->getReferenceName()) {
                    $currentCategory = $category;
                    break;
                }
            }

            if ($currentCategory instanceof  ProductCategory) {
                $currentCategory->addProduct($product);
                $this->getDoctrine()->getManager()->persist($currentCategory);
            }

            $newProducts[] = $product;
        }


        foreach ($newProducts as $product) {
            $ref = $product->__get('ref') ?? null;
            $refProduct = $socProductRepository->find($ref);
            $parent = null;

            foreach ($newProducts as $_product) {
                if($refProduct->getParent() !== null && $refProduct->getParent()->getReferenceName() ===$_product->getName()) {
                    $parent = $_product;
                    break;
                }
            }
            if($parent instanceof Product){
                $product->setParent($parent);
                $parent->addProduct($product);
                $this->getDoctrine()->getManager()->persist($parent);
            }

            $this->getDoctrine()->getManager()->persist($product);
        }



        try {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('info', "Created " . count($newProducts) . " products from previous schema");
        } catch (\Exception $exception) {
            $this->addFlash('danger', $exception->getMessage());
        }



        return $this->redirectToRoute('dash_product_index');
    }
}
