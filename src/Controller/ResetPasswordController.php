<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/mot-de-passe-oublie', name: 'app_reset_password')]

    public function index(Request $request): Response
    {
        if($this->getUser())
        {
            return $this->redirectToRoute('app_home');
        }

        if ($request->get('email')){
            $user = $this->em->getRepository(User::class)->findOneByEmail($request->get('email'));
                if ($user) {
                    // 1- Enregistrer en base la demande de reset_password avec user, token, createdAt.
                    $reset_password = new ResetPassword();
                    $reset_password->setUser($user);
                    $reset_password->setToken(uniqid());
                    $reset_password->setCreatedAt(new DateTimeImmutable());
                    $this->em->persist($reset_password);
                    $this->em->flush();

                     // 2- Envoyer un email à l'utilisateur avec un lien permettant de mettre à jour sont mot de passe
                        $url = $this->generateUrl('app_update_password', [
                            'token' => $reset_password->getToken()
                        ]);

                        $content="Bonjour ".$user->getFirstname().' '.$user->getLastname().",<br> à fin de réinitialiser votre mot de passe <br>";
                        $content.="Merci de bien vouloir cliquer sur le lien suivant: <a href=".$url."> pour mettre à jour votre mot de passe.";


                     $mail = new Mail();
                     $mail->send($user->getEmail(), $user->getFirstname().' '.$user->getLastname(), 'Réinitialiser votre mot de passe', $content);
                     $this->addFlash('notice', 'Vous allez recevoir dans quelques secondes un email avec la procédure pour réinitialiser votre mot de passe.');
                    
                    } else {

                    $this->addFlash('notice', 'Cette adresse email est inconnue.');
                }
        }

        return $this->render('reset_password/index.html.twig');
    }

    #[Route('/modifier-mon-mot-de-passe/{token}', name: 'app_update_password')]

    public function update(Request $request, $token,  UserPasswordHasherInterface $encoder): Response
    {
        $reset_password = $this->em->getRepository(ResetPassword::class)->findOneByToken($token);
            if (!$reset_password) {
                return $this->redirectToRoute('app_reset_password');
            }

            //Vérifier si le createdAt = now - 3h
            $now = new DateTimeImmutable();
            if ($now > $reset_password->getCreatedAt()->modify('+ 3 hour')){

                $this->addFlash('notice', 'Votre demande de mot de passe a expiré, Merci de la renouveler.');
               
                return$this->redirectToRoute('app_reset_password');
            }
    
                //Rendre une vue avec mot de passe et confirmer le nouveau passe.
                $form = $this->createForm(ResetPasswordType::class);
                $form->handleRequest($request);
                
                if ($form->isSubmitted() && $form->isValid()) {

                    $new_pwd = $form->get('new_password')->getData();

                 //Encodage du mot de passe
                 $password = $encoder->hashPassword($reset_password->getUser(), $new_pwd);
                 $reset_password->getUser()->setPassword($password);
                
                //Flush en base de donnée   
                    $this->em->flush();
                //Redirection de l'utilisateur vers la page de connexion.
                    $this->addFlash('notice', 'Votre mot de passe a bien été mis à jour');
                    return $this->redirectToRoute('app_login');

                
                }

                return $this->render('reset_password/update.html.twig', [
                    'form' => $form->createView()
                ]);
                         
    }
    
}
