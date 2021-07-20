<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use App\Entity\BookingPlace;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Entity\Place;
use App\Entity\Product;
use App\Entity\Space;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $routeBuilder = $this->get(AdminUrlGenerator::class);

        return $this->redirect($routeBuilder->setController(ProductCrudController::class)->generateUrl());

    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Gusto-Coffee - Administration');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Gérer les utilisateurs', 'fa fa-user', User::class);
        yield MenuItem::linkToCrud('Gérer Booking espaces', 'fas fa-book', Booking::class);
        yield MenuItem::linkToCrud('Gérer Booking places', 'fas fa-bookmark', BookingPlace::class);
        yield MenuItem::linkToCrud('Gérer les espaces privés', 'fas fa-users', Space::class);
        yield MenuItem::linkToCrud('Gérer les places publique', 'fas fa-chair', Place::class);
        yield MenuItem::linkToCrud('Gérer les produits', 'fab fa-shopify', Product::class);
        yield MenuItem::linkToCrud('Gérer les commandes', 'fa fa-shopping-cart', Order::class);
    }
}
