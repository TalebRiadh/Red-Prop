<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Basket;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Controller\admin\UserController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/connexion", name="connexion")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login_user(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $email = $authenticationUtils->getLastUsername();

        return $this->render(
            'security/login.html.twig',
            [
                'email' => $email,
                'error' => $error
            ]
        );

        return $this->redirectToRoute('home');

    }



    /**
     * @Route("/inscription", name="security_registration")
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */

    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface   $encoder)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $basket = new Basket();
            $user->setBasket($basket);
            $user->setRole(1);
            $hash = $encoder->hashPassword($user, $user->getPassword());
            $user->onPrePersist();
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('home');

        }
        return $this->render('security/Registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
