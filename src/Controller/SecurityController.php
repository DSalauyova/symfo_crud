<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     * Method Login
     * 
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * Method logout
     *
     * @return void
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Register method
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route(path: '/sign_in', name: 'app_registration', methods: ['GET', 'POST'])]
    //avec une option de mdp hashé
    public function registration(
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $hasher
    ): Response {
        $user = new User();
        //creation d'un Form avec info user
        $user->setRoles(['ROLE_USER']);
        $form = $this->createForm(RegistrationType::class, $user);
        //requete 
        $form->handleRequest($request);
        //si form a été soumis et validé par notre Rigister type(les contraints)
        if ($form->isSubmitted() && $form->isValid()) {
            // Hacher le mot de passe
            $hashedPassword = $hasher->hashPassword(
                $user,
                $user->getPlainPassword()
            );
            //pas confondre avec setPlainPass qui ne fonctionnera pas dans ce cas
            $user->setPassword($hashedPassword);
            //demander les données de form
            $user = $form->getData();

            //message sur la redirection
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Congratulation, You are One of us now!'
            );

            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
