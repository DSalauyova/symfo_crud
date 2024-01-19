<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/rgr', name: 'app_test')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller' => 'TestController is Work',
        ]);
    }
}
