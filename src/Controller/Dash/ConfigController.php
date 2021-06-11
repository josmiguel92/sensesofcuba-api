<?php

namespace App\Controller\Dash;

use App\Entity\ConfigValue;
use App\Entity\Product;
use App\Form\ConfigValueType;
use App\Form\ProductType;
use App\Repository\ConfigValueRepository;
use App\Repository\ProductRepository;
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
        return $this->render('dash/config/index.html.twig', [
            'configValues' => $configValues,
            'configValuesForm' => $configValuesForm->createView()
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
     * @Route("/{id}/edit", name="dash_product_category_edit", methods={"POST"})
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
}
