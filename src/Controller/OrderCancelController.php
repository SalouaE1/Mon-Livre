<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderCancelController extends AbstractController
{
    
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    } 


    #[Route('/commande/paiement-erreur/{reference}', name: 'stripe_error')]
    public function StripeError($reference, Cart $cart): Response
    {
        $order= $this->em->getRepository(Order::class)->findOneByReference($reference);

        if(!$order || $order->getUser() != $this->getUser() ){
            return $this->redirectToRoute('app_home');

         //envoyer un mail d'erreur paiement:
         $mail = new Mail();
         $content = 'Bonjour '.$order->getUser()->getFirstname().", <br> Votre commande n'est pas validée, un problème est survenue au momenet du paiement, merci de vérifier vos moyens de paiement et réessayer plus tard";

         $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), 'Erreur de paiement - Mon Livre', $content);
         
        }

    
        return $this->render('order_error/error.html.twig',[
            'order'=> $order
        ]);
    }
}
