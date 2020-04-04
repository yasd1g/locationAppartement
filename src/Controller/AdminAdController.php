<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads", name="admin_ads_index")
     */
    public function index(AdRepository $repo)
    {
        return $this->render('admin/ad/index.html.twig', [
            'ads' => $repo->findAll()
        ]);
    }

    /**
     * permet d'afficher le formulaire d'edition
     *
     * @param Ad $ad
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     */
    public function edit(Request $request, EntityManagerInterface $manager, Ad $ad)
    {
        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée !"
                );
        }

        return $this->render('admin/ad/edit.html.twig',
            [
                'ad' =>$ad,
                'form' => $form->createView()
            ]);

    }
}
