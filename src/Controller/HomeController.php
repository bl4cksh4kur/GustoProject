<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

     /**
     * @Route("/covid-news", name="covidnews")
     */
    public function covidnews(): Response
    {
        return $this->render('home/covid.html.twig');
    }


    /**
     * @Route("/foire-aux-questions", name="faq")
     */
    public function faq(): Response
    {
        return $this->render('home/faq.html.twig');
    }

    /**
     * @Route("/conditions-generales", name="conditions")
     */
    public function conditions(): Response
    {
        return $this->render('home/conditions.html.twig');
    }


}
