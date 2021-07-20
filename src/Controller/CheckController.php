<?php

namespace App\Controller;

use App\Entity\BookingPlace;
use App\Entity\Place;
use App\Form\PaymentPlaceType;
use App\Repository\BookingPlaceRepository;
use App\Repository\PlaceRepository;
use App\Form\PaymentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CheckController extends AbstractController
{
    private $place;
    private $session;
    private $bookingPlace;

    public function __construct(PlaceRepository $placeRepository, SessionInterface $session, BookingPlaceRepository $bookingPlaceRepository){

        $this->place = $placeRepository;
        $this->session = $session;
        $this->bookingPlace = $bookingPlaceRepository;
    }
    /**
     * @Route("/paiement-place-{title}", name="checkout_public_place")
     */
    public function index(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $addBooking = new BookingPlace();

        $place = $this->getDoctrine()->getRepository(Place::class)->find($this->session->get('place'));

        $locationDuration = (int) date_diff($this->session->get('startDate'), $this->session->get('endDate'))->format('%a%h');//Calcule la différence d'heure
        $startDate = $this->session->get('startDate'); // La variable contient la date passer en session de notre startDate
        $endDate = $this->session->get('endDate'); // La variable contient la session de notre endDate

        $placePrix = $place->getPrice();

        $prixSansReduction = $placePrix * $locationDuration;

        $heureDebutBooking = (int) date_format($this->session->get('startDate'), 'H:m'); // Afficher que l'heure
        $heureFinBooking = (int) date_format($this->session->get('endDate'), 'H:m');

        $debutHeureCreuse = "08:00";
        $finHeureCreuse = "17:00";

        $promoHour = 3; // Quand locationDuration aura atteint plus de 3h on aura droit à la promotion

        $heureCreuse = $heureDebutBooking > $debutHeureCreuse and $heureFinBooking < $finHeureCreuse; // ex: Si de 9h à 16H = heure creuse

        if ($heureCreuse ){
            if ($locationDuration == $promoHour){
                $prixTotal = ($placePrix * $locationDuration) - $placePrix;
            }else{
                $prixTotal = $prixSansReduction;
            }
        }else{
            $prixTotal = $prixSansReduction;
        }

        $form = $this->createForm(PaymentPlaceType::class, $addBooking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $addBooking->setUser($this->getuser());
            $addBooking->setplace($place);
            $addBooking->setStartDate($startDate);
            $addBooking->setEndDate($endDate);
            $addBooking->setLocationDuration($locationDuration);
            $addBooking->setTotal($prixTotal);

            $entityManager->persist($addBooking);
            $entityManager->flush();

            return $this->redirectToRoute('place_payment_success');
        }
        
        return $this->render('checkout2/index.html.twig', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'locationDuration' => $locationDuration,
            'place' => $place,
            'locationDuration' => $locationDuration,
            'prixSansReduction' => $prixSansReduction,
            'prixTotal' => $prixTotal,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/compte/reservation/confirmation", name="place_payment_success")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function paymentSuccess()
    {
        return $this->render('checkout2/success.html.twig');
    }
}
