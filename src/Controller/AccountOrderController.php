<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountOrderController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        return $this->em = $em;
    }


    #[Route('/compte/mes-commandes', name: 'app_account_order')]
    public function index(): Response
    {
        $orders=$this->em->getRepository(Order::class)->findSuccessOrders($this->getUser());



        return $this->render('account/order.html.twig',[
                'orders' => $orders
        ]);
    }

    
    #[Route('/compte/mes-commandes/{reference}', name: 'app_account_order_show')]
    public function show($reference): Response
    {
        $order=$this->em->getRepository(Order::class)->findOneByReference($reference);
        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('app_account_order');
        }


        return $this->render('account/order_show.html.twig',[
                'order' => $order
        ]);
    }




}
