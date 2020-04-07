<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminAdController extends AbstractController
{
    /**
     * permet d'afficher la liste des annonces
     *
     * le \d+ veut dire que le requirement page prend un nombre ou plus
     *
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index")
     */
    public function index(PaginationService $pagination, $page)
    {
        // ceil arrondi au nombre superieur cad si cest 3,5 on prendera 4
        // sans paginationService
        // $limit = 10;
        // $page = 1 ( 1ere page ) ===> 1*10 -10 = 0 donc start = 0
        // $page = 2 ( 2ieme page ) ===> 2*10 -10 = 10 donc start = 10   .... etc
        // $start = $page * $limit - $limit;

        // avec paginationService

        $pagination->setEntityClass(Ad::class)
                    ->setPage($page);

        return $this->render('admin/ad/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * permet d'afficher le formulaire d'edition
     *
     * @param Request                $request
     * @param EntityManagerInterface $manager
     * @param Ad                     $ad
     *
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     *
     * @return \Symfony\Component\HttpFoundation\Response
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

    /**
     * permet de supprimer une annonce
     *
     * @param EntityManagerInterface $manager
     * @param Ad                     $ad
     *
     * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(EntityManagerInterface $manager, Ad $ad)
    {
        if (count($ad->getBookings()) > 0){
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas supprimer l'annonce <strong>{$ad->getTitle()}</strong> car elle possède des réservations !"
            );
        } else {

            $manager->remove($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée !"
            );
        }

        return $this->redirectToRoute('admin_ads_index');
    }
}
