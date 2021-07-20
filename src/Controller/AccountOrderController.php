<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountOrderController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) 

    {
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("compte/mes-commandes", name="account_order")
     */
    public function index(): Response
    {
        $orders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($this->getUser());
        //findSuccessOrders fonction crée dans le Repository de Order
       

        return $this->render('account/order.html.twig', [
            'orders' => $orders
        ]);
    }

     /**
     * @Route("/compte/mes-commandes/{reference}", name="account_order_show")
     */
    public function show($reference)
    {

        $order = $this->entityManager->getRepository(Order::class)->findOneByReference($reference);
        //un order car c'est une seul commande

          // Si l'order n'éxiste pas ou que l'order getUser est différent de l'user connecter
        // Si c'est le cas redirection vers les commande
        if(!$order || $order->getUser() != $this->getUser())
        {
            return $this->redirectToRoute('account_order');
        }

        return $this->render('account/order_show.html.twig', [
            'order' => $order
        ]);
    }
}
