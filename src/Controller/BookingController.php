<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\BookingType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{

    /**
     * @param Request                $request
     * @param EntityManagerInterface $manager
     * @param Ad                     $ad
     *
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function book(Request $request, EntityManagerInterface $manager, Ad $ad)
    {
        $booking = new Booking();

        $form = $this->createForm(BookingType::class, $booking);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()){
            $user = $this->getUser();
            $booking->setBooker($user)
                    ->setAd($ad);

            //  si les dates ne sont pas disponibles ==> message d'erreur

            if(!$booking->isBookableDates()){
                $this->addFlash(
                    'warning',
                    "Les dates que vous avez choisi ne peuvent pas être réservées : elles cont déjà prises."
                    );
            } else {

            //  sinon enregistrement et redirection
            $manager->persist($booking);
            $manager->flush();

            return $this->redirectToRoute('booking_show', [
                'id' => $booking->getId(),
                'withAlert' => true
            ]);
            }
        }

        return $this->render('booking/book.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }


    /**
     * permet d'afficher la page d'une réservation
     *
     * @param Booking $booking
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/booking/{id}", name="booking_show")
     */
    public function show(Booking $booking)
    {
        return $this->render('booking/show.html.twig', [
            'booking' => $booking
        ]);
    }
}
