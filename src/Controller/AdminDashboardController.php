<?php

namespace App\Controller;

use App\Service\StatsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @param EntityManagerInterface $manager
     *
     * @Route("/admin", name="admin_dashboard")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(EntityManagerInterface $manager, StatsService $statsService)
    {
        $stats = $statsService->getStats();
        $bestAds = $statsService->getAdsStats('DESC');
        $worstAds = $statsService->getAdsStats('ASC');

        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $stats,
            'bestAds' =>$bestAds,
            'worstAds' =>$worstAds
        ]);
    }
}
