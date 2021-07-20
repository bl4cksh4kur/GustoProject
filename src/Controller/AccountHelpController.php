<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountHelpController extends AbstractController
{
    /**
     * @Route("/compte/centre-aide", name="account_help")
     */
    public function index(): Response
    {
        return $this->render('account/faq.html.twig');
    }
}
