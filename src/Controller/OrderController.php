<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/commande", name="order")
     */
    public function index(Cart $cart, Request $request): Response
    {   


        $form = $this->createForm(OrderType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
           
            $date = new DateTime();

            //Enregistrer ma commande (Order)
            $order = new Order();
            $reference = $date->format('dmY').'-'.uniqid();
            $order->setReference($reference);
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setIsPaid(1);

            $this->entityManager->persist($order);
            

            //Enregistrer mes produits (orderDetails)
            foreach($cart->getFull() as $product) {
            $orderDetails = new OrderDetails();
            $orderDetails->setMyOrder($order);
            $orderDetails->setProduct($product['product']->getName());
            $orderDetails->setQuantity($product['quantity']);
            $orderDetails->setPrice($product['product']->getPrice());
            $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
            $this->entityManager->persist($orderDetails);

            }


        $this->entityManager->flush();
        $cart->remove(); // Effacer le panier 
            
        return $this->redirectToRoute('order_success');
        }


        return $this->render('order/index.html.twig',[
            'form' => $form->createView(),
            'cart' => $cart->getFull()
        ]);
    }

      /**
     * @Route("/commande/confirmation", name="order_success")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function orderSuccess()
    {
        return $this->render('order/success.html.twig');
    }


}
