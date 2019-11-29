<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/home", name="homepage")
     */
    public function homepage(): \Symfony\Component\HttpFoundation\Response
    {
        return new RedirectResponse("index.html");
    }


    /**
     * @Route("/test", name="test")
     */
    public function test(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render("admin/index.html.twig");
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        return new RedirectResponse("index.html");
    }




}
