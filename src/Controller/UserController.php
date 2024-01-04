<?php

namespace App\Controller;


use App\Form\UserPasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


class UserController extends AbstractController
{
    /**
     * This method allow us to modify user data
     *
     * @param UserInterface $userInterface
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Route('/user/edit/{id}', name: 'edit_user', methods: ['GET', 'POST'])]
    public function editUser(
        UserInterface $userInterface,
        EntityManagerInterface $manager,
        Request $request,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        //avant on faisait -- aller recuperer le user dans la bd
        //$user = $repository->find($id);
        //de cette façon n'importe quel utilisateur peut changer les données de n'importe quel utilisateur et peut  faire ce qu'il veut ensuite
        //nous allons donc faire sorte que l'utilisateur puisse modifier que ses propres données à lui. Ctd verifier que le id dans url corresponde à celui connecté

        //si pas connecté, redigé sur la page de login
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        //creer un form avec cet objet
        $form = $this->createForm(UserType::class, $userInterface);
        //requete 
        $form->handleRequest($request);
        $user = $form->getData();
        //rajout d une condition : si le mot de pass soumis est le meme que dans la bd, on autorise la modif
        if ($form->isSubmitted() && $form->isValid()) {
            // password d'utilisateur entré com tel obtenu du form
            $submittedPassword = $form->get('plainPassword')->getData();
            //Hasher ce pass
            // $hashedPassword = $passwordHasher->hashPassword($user, $submittedPassword);
            //methode isPassValide prend user en question et mdp qu'il a saisi, verifie s'il correspond a celui stocké dans la bd
            if ($passwordHasher->isPasswordValid($user, $submittedPassword)) {
                $manager->persist($user);
                $manager->flush();
                //ajouter un message
                $this->addFlash(
                    'success',
                    'User has been modified'
                );
                //rediriger sur la page de recettes
                return $this->redirectToRoute('app_recipes');
            } else {
                $this->addFlash(
                    'danger',
                    'Password verification failed'
                );
            }
        }

        //return page avec le form d'edition de profil
        return $this->render('user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/user/edit_password/{id}', name: 'user_edit_pass', methods: ['GET', 'POST'])]
    public function editPassword(
        UserInterface $userInterface,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $manager
    ): Response {
        //syncroniser le FormeType de l'instance et controller
        //Creates and returns a Form instance from the type of the form.
        $form = $this->createForm(UserPasswordType::class, $userInterface);
        $form->handleRequest($request);
        $user = $form->getData();
        //post les deux pass entrés par user
        if ($form->isSubmitted() && $form->isValid()) {
            $old_pass = $form->getData()->getPlainPassword();
            $new_pass = $form->getData()->getNewPassword();
            //si le old pass correspond a celui de bd
            if ($passwordHasher->isPasswordValid($user, $old_pass)) {
                //alors on autorise de le changer par new pass avec les normes de security
                //hash new pass
                $new_hashed = $passwordHasher->hashPassword($user, $new_pass);
                //change old pass by new one
                $user->setPlainPassword($new_pass);
                $user->setPassword($new_hashed);
                //verify this new as simple pass
                // dd($user->getPlainPassword());
                // dd($old_pass, $new_pass, $new_hashed);
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Password has been modified'
                );
                return $this->redirectToRoute('app_recipes');
            } else {
                $this->addFlash(
                    'danger',
                    'Password verification failed'
                );
            }
        }

        return $this->render('user/edit_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
