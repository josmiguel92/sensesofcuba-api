<?php

namespace App\Controller\Dash;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashBoardController extends AbstractController
{
    /**
     * @Route("/dash/board", name="dash_board")
     */
    public function index(): Response
    {
        return $this->render('dash/board/index.html.twig', [
            'controller_name' => 'DashBoardController',
        ]);
    }
}
