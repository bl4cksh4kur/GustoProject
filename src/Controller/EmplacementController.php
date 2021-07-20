<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmplacementController extends AbstractController
{
    /**
     * @Route("/emplacement/", name="emplacement1")
     */
    public function index(): Response
    {
        return $this->render('emplacement/1.html.twig');
    }


       /**
     * @Route("/emplacement/2", name="emplacement2")
     */
    public function emplacement(): Response
    {
        return $this->render('emplacement/2.html.twig');
    }


       /**
     * @Route("/emplacement/3", name="emplacement3")
     */
    public function emplacement3(): Response
    {
        return $this->render('emplacement/3.html.twig');
    }


}
