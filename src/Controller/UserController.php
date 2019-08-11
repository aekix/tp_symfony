<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileUserType;
use App\Form\UserType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
        }
        $repository = $entityManager->getRepository(User::class)->findAll();
        return $this->render('user/index.html.twig', [
            'form' => $form->createView(),
            'users' => $repository,
        ]);
    }

    /**
     * @Route("/user/{id}", name="edit")
     *
     */
    public function edit(User $user, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
        }
        $repository = $entityManager->getRepository(User::class)->findAll();
        return $this->render('user/index.html.twig', [
            'form' => $form->createView(),
            'users' => $repository,
        ]);
    }

    /**
     * @Route("/user/remove/{id}", name="user_remove")
     */
    public function remove(User $user, EntityManagerInterface $entityManager)
    {
        $articles = $user->getArticles();
        foreach ($articles as $article) {
            $article->setUser(null);
        }
        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash('notice', 'Your changes were saved!');
        return $this->redirectToRoute('user');
    }

    /**
     * @Route("/user/{id}", name="u_details")
     *
     */
    public function detailsUser(User $user)
    {
        return $this->render('user/details.html.twig', [
            'user' => $user,
        ]);
    }
}
