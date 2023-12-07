<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    /**
     * method fetch all
     *
     * @param PaginatorInterface $paginator
     * @param RecipeRepository $recipeRepository
     * @param Request $request
     * @return Response
     */
    #[Route('/recipes', name: 'app_recipes', methods: ['GET'])]
    public function findAllRecipes(
        PaginatorInterface $paginator,
        RecipeRepository $recipeRepository,
        Request $request
    ): Response {
        $recipes = $paginator->paginate(
            //methode comprend query et request
            $recipeRepository->findAll(), /* $query */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit par page*/
        );
        return $this->render(
            'content/recipe/index.html.twig',
            [
                'recipes' => $recipes
            ]
        );
    }
    /**
     * method Create
     *
     * @param RecipeRepository $recipeRepository
     * @param integer $id
     * @return Response
     */
    #[Route('/recipes/create', name: 'new_recipe', methods: ['GET', 'POST'])]
    public function createNewRecipe(
        Request $request,
        EntityManagerInterface $manager
    ): Response {

        $recipe = new Recipe();
        //aller creer Formulaire; creation de Type dans bin/console make form
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // dd($form->getData());

            $recipe = $form->getData();
            $manager->persist($recipe);
            $manager->flush();
            $this->addFlash(
                "success",
                "Votre recette a bien été crée. "
            );

            return $this->redirectToRoute('app_recipes');
        }

        return $this->render(
            'content/recipe/new.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}
