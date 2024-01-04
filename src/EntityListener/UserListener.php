<?php
//espace des tous les events qui ont un event pour certaines entities 
// namespace App\EntityListener;

// use App\Entity\User;
// use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// //event pour User class, peut avoir plusieurs fonctions
// class UserListener
// {
//     private UserPasswordHasherInterface $hasher;
//     public function __construct(UserPasswordHasherInterface $hasher)
//     {
//         $this->hasher = $hasher;
//     }
//     //prend le user courant
//     public function prePersist(User $user)
//     {
//         $this->encodePassword($user);
//     }
//     //pour les mdp qui sont modifiés ou pour les creation des new (inscription),
//     //ces mdp passent une etape d'encodage
//     public function preUpadate(User $user)
//     {
//         $this->encodePassword($user);
//     }
//     /**
//      * Encode method based on basic password
//      *
//      * @param User $user
//      * @return void
//      */
//     public function encodePassword(User $user)
//     {
//         if ($user->getPlainPassword() === null) {
//             return;
//         }
//         $user->setPassword(
//             $this->hasher->hashPassword(
//                 $user,
//                 $user->getPlainPassword()
//             )
//         );
//     }
// }
