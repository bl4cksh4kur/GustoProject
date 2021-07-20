<?php

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart {

    private $session;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }


    public function getFull(){

        $cartComplete = [];

        if($this->get()) { // Si le panier n'est pas vide alors : 

            foreach($this->get() as $id => $quantity) {
                $product_object = $this->entityManager->getRepository(Product::class)->findOneById($id);

                if (!$product_object){ // Si le product_object n'éxiste pas

                    $this->delete($id);
                    continue;

                }

                $cartComplete[] = [
                    'product' => $product_object,
                    'quantity' => $quantity
                ];
            }

        }

        return $cartComplete;

    }



    public function add($id) // Ajouter via l'id
    {

        $cart = $this->session->get('cart', []); // Cherche la session cart

        if (!empty($cart[$id])) {  // Si tu as bien dans le panier un produit déjà insérer alors on ajoute une quantité 
            $cart[$id]++;
        } else {
            $cart[$id] = 1; // Si il y a rien d'ajouter alors ça reste 1
        }


        $this->session->set('cart', $cart);

    }



    public function get() // Récupérer
    {
        return $this->session->get('cart');
    }



    public function remove() // Supprimer
    {
        return $this->session->remove('cart');
    }



    public function delete($id) // Supprimer que par l'id
    {
        $cart = $this->session->get('cart', []);

        unset($cart[$id]); // Supprimer 

        return $this->session->set('cart', $cart); // Reset le nouveau
    }




    public function decrease($id) // Réduire ou Supprimer
    {

        $cart = $this->session->get('cart', []); // Cherche la session cart

        if ($cart[$id] > 1){ // Vérifie si la quantité du produit est supérieur à 1
            $cart[$id] --; // Retirer une quantité
        }
        else{ // Si la quantité n'est pas supérieur à 1 alors on supprime
            unset($cart[$id]); // Supprimer le produit
        }

        return $this->session->set('cart', $cart);


    }


}