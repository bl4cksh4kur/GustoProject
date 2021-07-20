<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    /**
     * @Route("/sitemap.xml", name="sitemap", defaults={"_format"="xml"})
     */
    public function index(Request $request)
    {   
        // On récup. le nom d'hôte depuis l'URL
        $hostname = $request->getSchemeAndHttpHost();

        // Initalise un tableau vide pour lister les URL
        $urls = []; // array()

        // On ajoute les URL
        $urls[] = ['loc' => $this->generateUrl('home')];
        $urls[] = ['loc' => $this->generateUrl('app_login')];
        $urls[] = ['loc' => $this->generateUrl('register')];
        $urls[] = ['loc' => $this->generateUrl('reset_password')];
        $urls[] = ['loc' => $this->generateUrl('space_private')];
        $urls[] = ['loc' => $this->generateUrl('space_public')];
        $urls[] = ['loc' => $this->generateUrl('product')];
        $urls[] = ['loc' => $this->generateUrl('covidnews')];
        $urls[] = ['loc' => $this->generateUrl('faq')];
        $urls[] = ['loc' => $this->generateUrl('conditions')];
        $urls[] = ['loc' => $this->generateUrl('emplacement1')];
        $urls[] = ['loc' => $this->generateUrl('emplacement2')];
        $urls[] = ['loc' => $this->generateUrl('emplacement3')];

        // Crée la réponse
        $response = new Response(
            $this->renderView('sitemap/index.html.twig', [
                'urls' => $urls,
                'hostname' => $hostname
            ])
        );

        // Ajout des entetes HTTP
        $response->headers->set('Content-Type', 'text/xml');

        // On envoi la réponse
        return $response;

    }
}
