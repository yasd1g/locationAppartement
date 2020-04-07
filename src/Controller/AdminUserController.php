<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminUserType;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserController extends AbstractController
{
    /**
     * permet d'afficher la liste des utilisateurs
     *
     * @Route("/admin/users/{page<\d+>?1}", name="admin_user_index")
     */
    public function index(PaginationService $pagination, $page)
    {
        $pagination->setEntityClass(User::class)
                    ->setPage($page);

        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * permet de modifier l'utilisateur
     *
     * @param Request                $request
     * @param EntityManagerInterface $manager
     * @param User                   $user
     *
     * @Route("/admin/users/{id}/edit",name="admin_user_edit")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(Request $request, EntityManagerInterface $manager, User $user)
    {
        $form = $this->createForm(AdminUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'utilisateur <strong>{$user->getFullName()}</strong> a bien été modifié."
            );

            return $this->redirectToRoute('admin_user_index');

        }
        return $this->render('admin/user/edit.html.twig', [
           'user' => $user,
           'form' => $form->createView()
        ]);
    }

//    /**
//     * permet de supprimer un utilisateur
//     *
//     * @param EntityManagerInterface $manager
//     * @param User                   $user
//     *
//     * @Route("/admin/users/{id}/delete", name="admin_user_delete")
//     *
//     * @return \Symfony\Component\HttpFoundation\RedirectResponse
//     */
//    public function delete(EntityManagerInterface $manager, User $user)
//    {
//        $manager->remove($user);
//        $manager->flush();
//
//        $this->addFlash(
//            'success',
//            "L'utilisateur a bien été supprimé !"
//        );
//
//        return $this->redirectToRoute('admin_user_index');
//    }
}
