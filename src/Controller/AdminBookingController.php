<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminBookingController extends AbstractController
{
    /**
     * permet d'afficher la liste des réservations
     *
     * @Route("/admin/bookings", name="admin_booking_index")
     */
    public function index(BookingRepository $repo)
    {

        return $this->render('admin/booking/index.html.twig', [
            'bookings' => $repo->findAll()
        ]);
    }

    /**
     * permet de modifier une réservation
     *
     * @Route("/admin/bookings/{id}/edit", name="admin_booking_edit")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Request $request, EntityManagerInterface $manager, Booking $booking)
    {
        $form = $this->createForm(AdminBookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            // il faut recalculer le montant de la réservation qd on modifie la réservation
            //on peut le faire de deux manieres
            // 1)  $booking->setAmount($booking->getAd()->getPrice() * $booking->getDuration());
            // 2) $booking->setAmount(0); et appeler la prepersist() qui sera appelee aussi pour le update

            $booking->setAmount(0);

            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le réservation numéro {$booking->getId()} a bien été modifiée ! "
            );

            return $this->redirectToRoute('admin_booking_index');
        }
        return $this->render('admin/booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form->createView()
        ]);
    }

    /**
     * permet de supprimer une réservation
     *
     * @param EntityManagerInterface $manager
     * @param Booking                $booking
     *
     * @Route("/admin/bookings/{id}/delete", name="admin_booking_delete")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(EntityManagerInterface $manager, Booking $booking)
    {
        $manager->remove($booking);
        $manager->flush();

        $this->addFlash(
            'success',
            "La réservation a bien été supprimée !"
        );
        return $this->redirectToRoute('admin_booking_index');

    }
}
