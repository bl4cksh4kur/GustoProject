<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Space;
use App\Repository\BookingRepository;
use App\Repository\ProductRepository;
use App\Repository\SpaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{


    private $space;
    private $session;
    private $booking;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, SpaceRepository $spaceRepository, SessionInterface $session, BookingRepository $bookingRepository){

        $this->space = $spaceRepository;
        $this->session = $session;
        $this->booking = $bookingRepository;
        $this->entityManager = $entityManager;

    }

    /**
     * @Route("/booking", name="booking")
     */
    public function index(): Response
    {
        $checkDate = $this->checkDate($this->session->get('startDate'), $this->session->get('endDate')); // Vérification avant l'affichage

        if ($checkDate)
        {
            $booking = $this->booking->findAll();

            $space = $this->space->findAll();

            $locationDuration = (int) date_diff($this->session->get('startDate'), $this->session->get('endDate'))->format('%a%h');//Calcule la différence d'heure
            $startDate = $this->session->get('startDate'); // La variable contient la date passer en session de notre startDate
            $endDate = $this->session->get('endDate'); // La variable contient la session de notre endDate

            $getSpace = $this->booking->getSpacesFree($startDate, $endDate);
            
            $idSpaceFree = array();
        foreach($getSpace as $obj) {
            array_push($idSpaceFree, $obj['title']);
        }

        return $this->render('booking/index.html.twig', [ //Passage des paramètres et Affichage des variables dans ma vue twig
            'startDate' => $startDate,
            'endDate' => $endDate,
            'locationDuration' => $locationDuration,
            'booking' => $booking,
            'space' => $space,
            'idSpaceFree' => $idSpaceFree
        ]);

        }else{
            return $this->redirectToRoute('space_private');
        }
    }

    /**
     * @Route("/reservation-{title}", name="reservation_salle_prive")
     */
    public function reservedPrivateOffice(Space $space, ProductRepository $products, Cart $cart): Response
    {
        $repo = $this->getDoctrine()->getRepository(Space::class);
        $show = $repo->find($space);


        $locationDuration = (int) date_diff($this->session->get('startDate'), $this->session->get('endDate'))->format('%a%h');//Calcule la différence d'heure
        $startDate = $this->session->get('startDate'); // La variable contient la date passer en session de notre startDate
        $endDate = $this->session->get('endDate');
        $this->session->set('space', $space);

        $spacePrix = $space->getPrice();

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

        
        

        return $this->render('booking/step2.html.twig', [
            'space' => $show,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'locationDuration' => $locationDuration,
            'prixSansReduction' => $prixSansReduction,
            'prixTotal' => $prixTotal,
            'cart' => $cart->getFull()

        ]);
    }

        

    public function checkDate($startDate, $endDate) {

        $today = new \DateTime('now'); // Récupère le jour et l'heure actuel

        if ($startDate < $today) {
            $this->addFlash("warning", "Vous ne pouvez pas réserver pour une date/heure antérieure à la date/heure actuelle.");
            return false;
        } elseif ($endDate < $startDate) {
            $this->addFlash("warning", "La date départ ne peut être inférieur à la date d'arrivée.");
            return false;
        } else {
            return true;
        }
    }


}
