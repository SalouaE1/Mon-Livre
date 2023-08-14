<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeController extends AbstractController
{
    private EntityManagerInterface $em;
    private $generator;
    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $generator)
    {
        $this->em = $em;
        $this->generator = $generator;
    }

    #[Route('/commande/create-session/{reference}', name: 'stripe_create_session')]

    public function index(EntityManagerInterface $em, Cart $cart, $reference): RedirectResponse
    {
        $order = $this->em->getRepository(Order::class)->findOneByReference($reference);

         if(!$order){
             return $this->redirectToRoute('app_cart');
         }

        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';


        foreach ($order->getOrderDetails()->getValues() as $product){
            $productData = $em->getRepository( Product::class )->findOneByName($product->getProduct());
             
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'EUR',
                    'unit_amount' =>$product->getPrice(),
                    'product_data'=>[
                        'name'=> $product->getProduct(),
                        'images'=> [$YOUR_DOMAIN."/uploads/".$productData->getIllustration()],
                    ],
                ],
                'quantity' => $product->getQuantity(),

            ];

        }
             $products_for_stripe[] = [
              'price_data' => [
                  'currency' => 'EUR',
                  'unit_amount' =>$order->getCarrierPrice(),
                  'product_data'=>[
                      'name'=>$order->getCarrierName(),
                  ],
              ],
              'quantity' => 1,

          ];

        

        $stripeSecretKey = 'sk_test_51NZI8SJcoQWBmfs2kmU428cPwf8pKpz2AxurDAxvu4ZXyBlXVNkOFML9l2AedooBvwU4oixYnhmFRQLc5NyO0Adm009VzMLGnz';
        Stripe::setApiKey($stripeSecretKey);

            

        $checkout_session = Session::create([
        'customer_email' => $this->getUser()->getEmail(),
         'payment_method_types' => ['card'],
         'line_items' => [   
            $products_for_stripe        
        ],
         'mode' => 'payment',
         'success_url' =>$this->generator->generate('stripe_success',[
            'reference'=> $order->getReference()
         ], UrlGeneratorInterface::ABSOLUTE_URL),

         'cancel_url' => $this->generator->generate('stripe_error',[
            'reference'=> $order->getReference()
         ], UrlGeneratorInterface::ABSOLUTE_URL)
         
        ]);
    
        return new RedirectResponse($checkout_session->url);

    }

}
