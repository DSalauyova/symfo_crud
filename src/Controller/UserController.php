<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * This method allow us to modify user data
     */
    #[Route('/user/edit/{id}', name: 'edit_user', methods: ['GET', 'POST'])]
    public function editUser(User $user, Request $request, EntityManagerInterface $manager): Response
    // (Request $request, EntityManagerInterface $manager): Response
    {
        //avant on faisait -- aller recuperer le user dans la bd
        //$user = $repository->find($id);
        //de cette façon n'importe quel utilisateur peut changer les données de n'importe quel utilisateur et peut  faire ce qu'il veut ensuite
        //nous allons donc faire sorte que l'utilisateur puisse modifier que ses propres données à lui. Ctd verifier que le id dans url corresponde à celui connecté

        //user correspondant à ID dans le url, trouvé avec paramConverter
        // dd($user);
        //user qui est connecté
        // dd($this->getUser()); //method de AbstractController

        //si user veut modifier un profil d'un autre user via url https://localhost:8000/user/edit/117, il sera rediriger a la page de connexion
        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('app_login');
        }
        //si user accede a la modif de son profil (avec son id), il sera redirigé à la page de recettes
        if ($this->getUser() === $user) {
            return $this->redirectToRoute('app_recipes');
        }


        //creer un form avec cet objet
        $form = $this->createForm(UserType::class, $user);
        //requete 
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //demander les données de form
            $user = $form->getData();
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'User has been modified'
            );
            return $this->redirectToRoute('app_recipes');
        }
        return $this->render('user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
