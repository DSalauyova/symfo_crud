<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(RecipeRepository $repository): Response
    {
        //call repository methode handmade to show 3 last recipes
        return $this->render(
            '/content/home/index.html.twig',
            [
                'controller_name' => "Hey You",
                'public_recipes' => $repository->findPublicRecipe(3)
            ]
        );
    }
}
