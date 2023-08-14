<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/inscription', name: 'app_register')]

    public function index(Request $request, UserPasswordHasherInterface $encoder): Response
    {
        $notification = null;
        $user= new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form-> isValid()) {
            $user = $form->getData();
            
            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());
           
            if(!$search_email){
                $password = $encoder->hashPassword($user,$user->getPassword());
                $user->setPassword($password);
                    
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $mail = new Mail();
                $content = 'Bonjour '.$user->getFirstname().', <br> Bienvenue chez Mon Livre. <br> Votre compte vous donne accès à découvrir un large choix de livres de poche et à partager avec une communauté de lecteurs passionnés!';

                $mail->send($user->getEmail(), $user->getFirstname(), "Confirmation d'inscription - Mon Livre", $content);
                
                $notification = "Votre inscription s'est bien déroulée. vous pouvez dès à présent vous connecter à votre compte";

            } else {
                $notification = 'Cet email existe déjà';

            }

        }
           
       return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
