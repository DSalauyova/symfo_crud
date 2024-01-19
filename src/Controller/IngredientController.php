<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{

    /**
     *
     * READ
     *
     * find liste des ingredients function
     *
     * @param IngredientRepository $getRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */

    #[Route('/ingredients', name: 'app_ingredients', methods: 'GET')]
    #[IsGranted('ACCES_PAGES')]

    //importer la repository class et la nommer, injection de la dependence
    //on importe aussi PaginatorInterface pour rajouter la pagination pour le tableau
    public function findAllIngredients(IngredientRepository $getRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            //methode comprend query et request
            //$getRepository->findAll(), /* $query */
            //afficher ingred uniquement reliés a user courant
            $getRepository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit par page*/
        );
        return $this->render(
            'content/ingredient/index.html.twig',
            [
                'ingredients' => $ingredients
            ]
        );
    }

    //read/find one element (cRud)

    // #[Route('/ingredient/{id}', name: 'app_ingredient')]
    // public function findById(IngredientRepository $itemIngredient, int $id): Response
    // {
    //     $oneIngredient = $itemIngredient
    //         ->find($id);
    //     return $this->render(
    //         'content/ingredient/index.html.twig',
    //         ['oneIngredient' => $oneIngredient]
    //     );
    // }


    /**
     * CREATE element
     * function createNewIngredient
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route(path: 'ingredient/create', name: 'ingredient.new', methods: ['GET', 'POST'])]
    //request en param pour recuperer request en POST quand on a soumit le form
    public function createNewIngredient(
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        //creer un objet de la classe Ingredient(importer Ingredient de Entity)
        $ingredient = new Ingredient();
        //creer formulaire avec la method createForm donné par AbstractController
        $form = $this->createForm(IngredientType::class, $ingredient);
        //recuperer les données request
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $ingredient->setUser($this->getUser());
            //sent data like commit and push
            $manager->persist($ingredient);
            //push
            $manager->flush();
            $this->addFlash(
                "success",
                "Votre ingredient a bien été crée. "
            );
            return $this->redirectToRoute('app_ingredients');
        }
        //retourner la vue twig
        return $this->render(
            'content/ingredient/new.html.twig',
            [
                //method to render the form
                'form' => $form->createView()
            ]
        );
    }

    /**
     * UPDATE element
     * @param integer $id
     * @param IngredientRepository $repo
     * @return Response
     * il existe 2 possibilités : soit recuperer l'objet par son id, soit le faire automtiquement en passant objet en parametre cela necessite autowiring, en utilisant un paramètre de convertisseur de route. Cela permettrait de définir comment Symfony doit récupérer l'objet Ingredient en fonction de l'identifiant dans l'URL. Mais il faut creer un convertisseur de route.
     * on ne va pas s'embeter et preferons la methode scolaire avec id
     */
    #[Route('/ingredient/edit/{id}', name: 'ingredient_edit', methods: ['GET', 'POST'])]
    public function editIngredient(
        int $id,
        IngredientRepository $repo,
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        $ingredient = $repo->find($id);
        // $ingredient est maintenant correctement récupéré
        $form = $this->createForm(IngredientType::class, $ingredient);
        //gerer la modification d'un elem et interagir avec la base
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            //send data
            $manager->persist($ingredient);
            //push
            $manager->flush();
            $this->addFlash(
                "success",
                "L'ingredient a été modifié !"
            );
            return $this->redirectToRoute('app_ingredients');
        }
        //retourner la vue twig
        return $this->render(
            'content/ingredient/edit.html.twig',
            [
                //method to render the form
                'form' => $form->createView()
            ]
        );
        // return $this->render('chemin_vers_votre_vue.html.twig', [
        //     'ingredient' => $ingredient,
        // ]);
    }

    /**
     * DELETE function
     *
     * @param EntityManagerInterface $manager
     * @param IngredientRepository $ingredientRepository
     * @param integer $id
     * @return Response
     */

    #[Route('ingredient/delete/{id}', name: 'ingredient_delete', methods: ['GET'])]
    public function deleteIngredient(
        EntityManagerInterface $manager,
        IngredientRepository $ingredientRepository,
        int $id
    ): Response {
        $ingredient = $ingredientRepository->find($id);
        //si l'ingredient n'existe pas, afficher un message et rediriger
        if (!$ingredient) {
            $this->addFlash(
                'danger',
                'L\'ingredient n\'a pas été trouvé'
            );
            return $this->redirectToRoute('app_ingredients');
        }
        //sinon supprimer
        $manager->remove($ingredient);
        $manager->flush();
        $this->addFlash(
            'success',
            'Ingredient ' . $id . ' a été supprimé'
        );
        return $this->redirectToRoute('app_ingredients');
    }
}
