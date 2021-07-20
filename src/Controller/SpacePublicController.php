<?php

namespace App\Controller;

use App\Form\BookingPlaceType;
use App\Repository\PlaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class SpacePublicController extends AbstractController
{

    private $place;
    private $session;

    public function __construct(PlaceRepository $placeRepository, SessionInterface $session)
    {
        $this->session = $session;
        $this->place = $placeRepository;

    }

    /**
     * @Route("/espace-publique", name="space_public")
     */
    public function index(Request $request): Response
    {

        $form = $this->createForm(BookingPlaceType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $bookingPlaceStartDate = $form->getData()->getStartDate();
            $bookingPlaceEndDate = $form->getData()->getEndDate();
            
            $this->session->set('startDate', $bookingPlaceStartDate);
            $this->session->set('endDate', $bookingPlaceEndDate);

            return $this->redirectToRoute('booking_place');

        }


        return $this->render('space_public/index.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
