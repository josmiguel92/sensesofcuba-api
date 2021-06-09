<?php

namespace App\Controller\Dash;

use App\Entity\ConfigValue;
use App\Entity\Product;
use App\Entity\ProductCategory;
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
}
