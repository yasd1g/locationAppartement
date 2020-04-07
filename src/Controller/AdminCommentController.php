<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommentController extends AbstractController
{
    /**
     * permet d'afficher la liste des commentaires
     *
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comment_index")
     */
    public function index(PaginationService $pagination, $page)
    {
        $pagination->setEntityClass(Comment::class)
            ->setPage($page);

        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $pagination
        ]);
    }


    /**
     * permet d'afficher le formulaire d'edition
     *
     * @param Request                $request
     * @param EntityManagerInterface $manager
     * @param Comment                $comment
     *
     * @Route("/admin/comments/{id}/edit", name="admin_comment_edit")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Request $request, EntityManagerInterface $manager, Comment $comment)
    {
        $form = $this->createForm(AdminCommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le commentaire numéro {$comment->getId()} a bien été modifié !"
            );
        }

        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView()
        ]);
    }

    /**
     * permet de supprimer un commentaire
     *
     * @param Request                $request
     * @param EntityManagerInterface $manager
     * @param Comment                $comment
     *
     * @Route("/admin/comments/{id}/delete", name="admin_comment_delete")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, EntityManagerInterface $manager, Comment $comment)
    {
        $manager->remove($comment);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le commentaire de {$comment->getAuthor()->getFullName()} a bien été supprimé !"
        );

        return $this->redirectToRoute('admin_comment_index');
    }
}
