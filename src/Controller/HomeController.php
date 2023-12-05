<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {
    #[Route('/home', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        //en params le chemin sur la vue et une response
        return $this->render('/content/home/index.html.twig', array(
            'controller_name' => 'Hey You',
        ));
    }
}


// <h1>{{ controller_name }}!</h1>