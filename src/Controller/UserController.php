<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * permet d'afficher la page profil de n'importe quel utilisateur ( non authentifié)
     *
     * @Route("/user/{slug}", name="user_show")
     */
    public function index(User $user)
    {


        return $this->render('user/index.html.twig', [
            'user' => $user
        ]);
    }
}
