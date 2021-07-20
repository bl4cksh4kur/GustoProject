<?php

namespace App\Controller;

use App\Form\BookingType;
use App\Repository\SpaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SpacePrivateController extends AbstractController
{

    private $space; // Initialisation
    private $session; // Initialisation

    public function __construct(SpaceRepository $spaceRepository, SessionInterface $session)
    {
        $this->session  = $session;
        $this->space    = $spaceRepository;
    }

    /**
     * @Route("/espace-prive", name="space_private")
     */
    public function index(Request $request): Response
    {

        $form = $this->createForm(BookingType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $bookingStartDate = $form->getData()->getStartDate(); // Récupère les données via getData
            $bookingEndDate = $form->getData()->getEndDate(); // Récupère les données via getData
            $this->session->set('startDate', $bookingStartDate); // set startDate en session, bookingStartDate = la date choisi
            $this->session->set('endDate', $bookingEndDate); // set endDate en session

            return $this->redirectToRoute('booking'); // Redirection si c'est bon

        }


        return $this->render('space_private/index.html.twig',[
            'form' => $form->createView()
        ]);
    }


}
