<?php

namespace App\Controller;

use App\Entity\Place;
use App\Repository\BookingPlaceRepository;
use App\Repository\PlaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class BookingPlaceController extends AbstractController
{

    private $place;
    private $session;
    private $bookingPlace;

    public function __construct(PlaceRepository $placeRepository, SessionInterface $session, BookingPlaceRepository $bookingPlaceRepository)
    {
        $this->place = $placeRepository;
        $this->session = $session;
        $this->bookingPlace = $bookingPlaceRepository;


    }

    /**
     * @Route("booking/place", name="booking_place")
     */
    public function index(): Response
    {
        $checkDate = $this->checkDate($this->session->get('startDate'), $this->session->get('endDate'));

        if($checkDate){

        $bookingPlace = $this->bookingPlace->findAll();

        $place = $this->place->findAll();

        $locationDuration = (int)date_diff($this->session->get('startDate'), $this->session->get('endDate'))->format('%a%h');
        
        $startDate = $this->session->get('startDate');
        $endDate = $this->session->get('endDate');

        $getPlace = $this->bookingPlace->getPlacesFree($startDate, $endDate);

        $idPlaceFree = array();
        foreach($getPlace as $obj) {
            array_push($idPlaceFree, $obj['title']);
        }


        return $this->render('booking_place/index.html.twig',[
            'startDate' => $startDate,
            'endDate' => $endDate,
            'locationDuration' => $locationDuration,
            'bookingPlace' => $bookingPlace,
            'place' => $place,
            'idPlaceFree' => $idPlaceFree
        ]);

        } else {

            return $this->redirectToRoute('space_public');
        }
    }
    
     /**
     * @Route("booking/place/reservation-{title}", name="booking_place_reservation")
     */
    public function reservedPlace(Place $place): Response
    {
        $repo = $this->getDoctrine()->getRepository(Place::class);
        $show = $repo->find($place);

        $locationDuration = (int) date_diff($this->session->get('startDate'), $this->session->get('endDate'))->format('%a%h');//Calcule la différence d'heure
        $startDate = $this->session->get('startDate'); // La variable contient la date passer en session de notre startDate
        $endDate = $this->session->get('endDate');
        $this->session->set('place', $place);

        $spacePrix = $place->getPrice();

        $prixSansReduction = $spacePrix * $locationDuration;

        $heureDebutBooking = (int) date_format($this->session->get('startDate'), 'H:m'); // Afficher que l'heure
        $heureFinBooking = (int) date_format($this->session->get('endDate'), 'H:m');

        $debutHeureCreuse = "08:00";
        $finHeureCreuse = "17:00";

        $promoHour = 3; // Quand locationDuration aura atteint plus de 3h on aura droit à la promotion

        $heureCreuse = $heureDebutBooking > $debutHeureCreuse and $heureFinBooking < $finHeureCreuse; // ex: Si de 9h à 16H = heure creuse

        if ($heureCreuse ){
            if ($locationDuration >= $promoHour){
                $prixTotal = ($spacePrix * $locationDuration) - $spacePrix;
            }else{
                $prixTotal = $prixSansReduction;
            }
        }else{
            $prixTotal = $prixSansReduction;
        }

        return $this->render('booking_place/step2.html.twig', [
            'place' => $show,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'locationDuration' => $locationDuration,
            'prixSansReduction' => $prixSansReduction,
            'prixTotal' => $prixTotal,
        ]);
    }
    


    public function checkDate($startDate, $endDate) {

        $today = new \DateTime('now'); // Récupère le jour et l'heure actuel

        if ($startDate < $today) {
            $this->addFlash("warning", "Vous ne pouvez pas réserver pour une date antérieure à la date actuelle.");
            return false;
        } elseif ($endDate < $startDate) {
            $this->addFlash("warning", "La date départ ne peut être inférieur à la date d'arrivée.");
            return false;
        } else {
            return true;
        }
    }


}

