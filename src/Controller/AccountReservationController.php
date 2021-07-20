<?php

namespace App\Controller;


use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountReservationController extends AbstractController
{

    /**
     * @Route("/compte/mes-reservations", name="account_reservation")
     */
    public function index(): Response
    {
        return $this->render('account/reservations.html.twig');
    }
}
