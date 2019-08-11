<?php

namespace App\Controller;
use App\Form\ArticleType;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="article")
     */
    public function index(Request $request)
    {

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager->persist($article);
            $entityManager->flush();
        }

        $repository = $entityManager->getRepository(Article::class)->findAll();
        return $this->render('article/index.html.twig', [
            'form' => $form->createView(),
            'articles' => $repository,
        ]);
    }
}
