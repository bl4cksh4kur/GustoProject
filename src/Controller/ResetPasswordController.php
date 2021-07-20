<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{   
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) 
    {
        $this->entityManager = $entityManager;
    }



    /**
     * @Route("/mot-de-passse-oublie", name="reset_password")
     */
    public function index(Request $request): Response
    {

        if($this->getUser()){ // SI l'user est déjà connecter alors redirection
            return $this->redirectToRoute('home');
        }


        if($request->get('email')){

            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));
          
            if($user){
                // 1 : Enregistrer en base la demande de reset avec user, token, createdAt
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new DateTime());
                $this->entityManager->persist($reset_password);
                $this->entityManager->flush();
                
                //2 : Envoyer un mail à l'utilisateur un lien pour reset le password
                
                //Lien pour le mail
                $url = $this->generateUrl('update_password', [
                    'token' => $reset_password->getToken()
                ]);



                $content = "Bonjour ".$user->getFirstname()."<br/><br/>Vous avez demandé à réinialiser votre mot de passe sur le site Gusto-Coffee.<br/><br/>";
                $content.= "Merci de bien vouloir cliquer sur le lien suivant pour <a href='https://gusto-cowork.fr".$url."'> mettre à jour votre mot de passe</a>.";

                $mail = new Mail();
                $mail->send($user->getEmail(), $user->getFirstname().' '.$user->getLastname(), 'Réinitialiser votre mot de passe sur Gusto-Coffee !', $content );
                
                $this->addFlash('notice', 'Vous allez recevoir un mail pour modifier votre mot de passe.');
            } else {
                $this->addFlash('notice', 'Cette adresse email est inconnue.');
            }

        }


        return $this->render('reset_password/index.html.twig');
    }


     /**
     * @Route("/mot-de-passse-oublie/{token}", name="update_password")
     */

     public function update(Request $request, $token, UserPasswordEncoderInterface $encoder)
     {
            $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneByToken($token);

            if(!$reset_password){
                return $this->redirectToRoute('reset_password');
            }
            
            //Vérifier si le createdAt = now - 3h
            $now = new \DateTime();
            if($now > $reset_password->getCreatedAt()->modify('+ 3 hour')){
               
                //TokenExpirer
                $this->addFlash('notice', 'Votre demande de changement de mot de passe a expiré. Merci de la renouveller.');
                return $this->redirectToRoute('reset_password');

            }

            // Rendre une vue avec mot de passe et confirmation
            $form = $this->createForm(ResetPasswordType::class);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                
                $new_pwd = $form->get('new_password')->getData();
            
                
            // Encodage du mot de passe
                $password = $encoder->encodePassword($reset_password->getUser(), $new_pwd);

            //SET 
            $reset_password->getUser()->setPassword($password);

            // Flush
            $this->entityManager->flush();

            // Redirection sur la page de connexion
            $this->addFlash('notice', 'Votre mot de passe a bien été mis à jour.');
            return $this->redirectToRoute('app_login');


            }
            return $this->render('reset_password/update.html.twig',[
                'form' => $form->createView()
            ]);

     }

}
