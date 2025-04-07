<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeControlleController extends AbstractController{
    #[Route('/', name: 'app_home_controlle')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'HomeControlleController',
        ]);
    }

    #[Route('/test', name: 'app_home_controllee')]
    public function test(): Response
    {
        return $this->render('home_controlle/test.html.twig', [
            'controller_name' => 'HomeControlleController',
        ]);
    }
}
