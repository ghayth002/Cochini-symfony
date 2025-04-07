<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route('/reclamationtest', name: 'app_reclamation_test')]
    public function index(): Response
    {
        return new Response('Reclamation test route works!');
    }

    #[Route('/admintest', name: 'app_admin_test')]
    public function admin(): Response
    {
        return new Response('Admin test route works!');
    }
} 