<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountPasswordController extends AbstractController
{
    #[Route('/compte/modifier-mot-de-passe', name: 'app_account_password')]
    public function index(Request $request, UserPasswordHasherInterface $encoder, EntityManagerInterface $entityManager): Response
    {

        $notification = null;
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPwd = $form->get('old_password')->getData();

            if ($encoder->isPasswordValid($user, $oldPwd)) {
                $newPwd = $form->get('new_password')->get('first')->getData();
                $hashedPassword = $encoder->hashPassword($user, $newPwd);
                $user->setPassword($hashedPassword);

                $entityManager->persist($user);
                $entityManager->flush();
                $notification = "votre mot de passe a bien été mis à jour";
            } else{
                $notification = "Votre mot de passe actuel n'est pas le bon";
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}


