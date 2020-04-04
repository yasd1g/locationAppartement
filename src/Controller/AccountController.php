<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    /**
     * permet d'afficher le formulaire de login et de se connecter
     *
     * @Route("/login", name="account_login")
     */
    public function login(AuthenticationUtils $utils)
    {
        //$error sera égale à null s'il n'ya pas d'erreur
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        return $this->render('account/login.html.twig',[
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    /**
     * permet de se deconnecter
     *
     * @Route("/logout", name="account_logout")
     */
    public function logout()
    {
        //rien à ecrire tout se passe derriere le rideau grace a symfony et le security.yaml
    }

    /**
     * permet d'afficher le formulaire d'inscription et de s'inscrire
     *
     * @Route("/register", name="account_register")
     *
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $hash = $encoder->encodePassword($user,$user->getHash());
            $user->setHash($hash);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success',
                "Votre compte a bien été créé ! Vous pouvez maintenant vous connectez !");

            return  $this->redirectToRoute('account_login');
        }

        return $this->render('/account/registration.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * permet d'afficher et de traiter le formulaire de modification de profil
     *
     * @Route("/account/profile", name="account_profile")
     * @IsGranted("ROLE_USER")
     *
     */
    public function profile(Request $request, EntityManagerInterface $manager)
    {

        $user = $this->getUser();
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success',
                "Les données du profil ont été enregistrées avec succès ");
        }
        return $this->render('account/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * permet de modifier le mot de passe
     *
     * @Route("/account/password-update", name="account_password")
     * @IsGranted("ROLE_USER")
     *
     */
    public function updatePassword(Request $request, EntityManagerInterface $manager,UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getUser();
        $passwordUpdate = new PasswordUpdate();
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            //1.verifier si le mot de passe oldPassword est bien le bon passeword de l'utilisateur

            if(!password_verify($passwordUpdate->getOldPassword(), $user->getHash())){

                //gérer l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez entré n'est pas votre mot de passe actuel !"));

            }else{

                $newPassword = $encoder->encodePassword($user,$passwordUpdate->getNewPassword());
                $user->setHash($newPassword);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success',
                    "Votre mot de passe a bien été modifié !"
                );

                return $this->redirectToRoute('homepage');
            }

        }
        return $this->render('/account/password.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * permet d'afficher le profil de l'utilisateur connecté
     *
     * @Route("/account", name="account_index")
     * @IsGranted("ROLE_USER")
     *
     */
    public function myAccount()
    {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    /**
     * premet d'afficher la liste des réservations faites par l'utilisateur
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/account/bookings", name="account_bookings")
     */
    public function bookings()
    {
        return $this->render('account/bookings.html.twig');
    }

}
