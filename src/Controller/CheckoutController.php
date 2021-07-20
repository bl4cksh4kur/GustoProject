<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Space;
use App\Form\PaymentType;
use App\Repository\BookingRepository;
use App\Repository\SpaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    private $space;
    private $session;
    private $booking;

    public function __construct(SpaceRepository $spaceRepository, SessionInterface $session, BookingRepository $bookingRepository){

        $this->space = $spaceRepository;
        $this->session = $session;
        $this->booking = $bookingRepository;
    }
    /**
     * @Route("/paiement-salle-prive-{title}", name="checkout_private_room")
     */
    public function index(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $addBooking = new Booking();

        $space = $this->getDoctrine()->getRepository(Space::class)->find($this->session->get('space'));

        $locationDuration = (int) date_diff($this->session->get('startDate'), $this->session->get('endDate'))->format('%a%h');//Calcule la différence d'heure
        $startDate = $this->session->get('startDate'); // La variable contient la date passer en session de notre startDate
        $endDate = $this->session->get('endDate'); // La variable contient la session de notre endDate

        $spacePrix = $space->getPrice();

        $prixSansReduction = $spacePrix * $locationDuration;

        $heureDebutBooking = (int) date_format($this->session->get('startDate'), 'H:m'); // Afficher que l'heure
        $heureFinBooking = (int) date_format($this->session->get('endDate'), 'H:m');

        $debutHeureCreuse = "08:00";
        $finHeureCreuse = "17:00";

        $promoHour = 3; // Quand locationDuration aura atteint plus de 3h on aura droit à la promotion

        $heureCreuse = $heureDebutBooking > $debutHeureCreuse and $heureFinBooking < $finHeureCreuse; // ex: Si de 9h à 16H = heure creuse

        if ($heureCreuse ){
            if ($locationDuration == $promoHour){
                $prixTotal = ($spacePrix * $locationDuration) - $spacePrix;
            }else{
                $prixTotal = $prixSansReduction;
            }
        }else{
            $prixTotal = $prixSansReduction;
        }

        $form = $this->createForm(PaymentType::class, $addBooking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $addBooking->setUser($this->getuser());
            $addBooking->setSpace($space);
            $addBooking->setStartDate($startDate);
            $addBooking->setEndDate($endDate);
            $addBooking->setLocationDuration($locationDuration);
            $addBooking->setTotal($prixTotal);

            $entityManager->persist($addBooking);
            $entityManager->flush();

            return $this->redirectToRoute('payment_success');
        }
        
        return $this->render('checkout/index.html.twig', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'locationDuration' => $locationDuration,
            'space' => $space,
            'locationDuration' => $locationDuration,
            'prixSansReduction' => $prixSansReduction,
            'prixTotal' => $prixTotal,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/compte/reservation/confirmation", name="payment_success")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function paymentSuccess()
    {
        return $this->render('checkout/success.html.twig');
    }
}
