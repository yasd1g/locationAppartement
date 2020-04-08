<?php

namespace App\Controller;


use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function home(AdRepository $adRepo, UserRepository $userRepo)
    {

        $ads = $adRepo->findBestAds(3);
        $users = $userRepo->findBestUsers(2);
//        dump($users);
//        die();
        return $this->render("home.html.twig", [
            'ads' => $ads,
            'users' => $users
            ]);
    }
}
