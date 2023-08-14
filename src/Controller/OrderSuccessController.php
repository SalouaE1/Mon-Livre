<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderSuccessController extends AbstractController
{

    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    } 

    #[Route('/commande/paiement-success/{reference}', name: 'stripe_success')]
    public function StripeSuccess($reference, Cart $cart): Response
    {
        $order= $this->em->getRepository(Order::class)->findOneByReference($reference);

        if(!$order || $order->getUser() != $this->getUser() ){
            return $this->redirectToRoute('app_home');
        }

        if($order->getState() ==0){
            //vider la session "cart"- vider le panier
            $cart->remove();

            $order->setState(1);
            $this->em->flush();

            //envoyer un mail de confirmation:
            $mail = new Mail();
            $content = 'Bonjour '.$order->getUser()->getFirstname().', <br> Votre commande est bien validée, on espère vous revoir prochainement pour une nouvelle commande';

            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), "Merci pour votre commande", $content);
            

        }
        return $this->render('order_success/success.html.twig',[
            'order'=>$order
        ] );
    }
}
    