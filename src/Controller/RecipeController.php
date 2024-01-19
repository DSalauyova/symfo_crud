<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    /**
     * method fetch all (READ)
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
            $recipeRepository->findBy(['user' => $this->getUser()]), /* $query */
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
     * method CREATE
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
            $recipe->setUser($this->getUser());
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

    /**
     * method UPDATE
     *
     * @param EntityManagerInterface $manager
     * @param RecipeRepository $repository
     * @param Request $request
     * @param integer $id
     * @return Response
     */
    #[Route(path: '/recipes/edit/{id}', name: 'edit_recipe', methods: ['GET', 'POST'])]
    public function editRecipe(
        EntityManagerInterface $manager,
        RecipeRepository $repository,
        Request $request,
        int $id
    ): Response {
        $recipe = $repository->find($id);
        $form = $this->createForm(RecipeType::class, $recipe);
        //fait apparraitre les données dans le formumlaire
        $form->handleRequest($request);
        //2 etape validation de formulaire; il enverra les données du formulaire dans la bdd
        $recipe = $form->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($recipe);
            // ce message se rajoutera aux changements
            $manager->flush();
            $this->addFlash(
                "success",
                "yeah, New recipe added in the list. "
            );
            return $this->redirectToRoute('app_recipes');
        }
        //1 etape - retourner la vue edit.html.twig; la vue appellera la variable(clé) 'form' et cette dernière creera un formulaire
        return $this->render(
            'content/recipe/edit.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
    /**
     * handleRequest est une étape dans le cycle de vie du formulaire Symfony. Elle prend les données de la requête, les lie au formulaire, effectue la validation et met à jour l'état du formulaire. Cela permet de préparer le formulaire pour être utilisé dans le contrôleur, par exemple, pour effectuer des actions basées sur les données soumises par l'utilisateur.
     */

    /**
     * DELETE function
     * @param EntityManagerInterface $manager
     * @param RecipeRepository $recipeRepository
     * @param integer $id
     * @return Response
     */
    #[Route('recipes/delete/{id}', name: 'delete_recipe', methods: ['GET'])]
    public function deleteRecipe(
        EntityManagerInterface $manager,
        RecipeRepository $recipeRepository,
        int $id
    ): Response {
        $recipe = $recipeRepository->find($id);
        //si n'existe pas, afficher un message et rediriger
        if (!$recipe) {
            $this->addFlash(
                'danger',
                'Recipe doesn\'t exist'
            );
            return $this->redirectToRoute('app_recipes');
        }
        //sinon supprimer
        $manager->remove($recipe);
        $manager->flush();
        $this->addFlash(
            'danger',
            'Recipe ' . $id . ' is deleted !'
        );
        return $this->redirectToRoute('app_recipes');
    }
}
