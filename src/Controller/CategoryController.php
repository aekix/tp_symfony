<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\CardScheme;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category/{id}", name="v_categorie")
     */
    public function detailsUser(Category $category)
    {
        return $this->render('category/details.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("/listCategory", name="list_category")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listUser(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Category::class)->findAll();
        return $this->render('admin/list_category.html.twig', [
            'category' => $repository,
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @Route("/addCateg", name="add_category")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addCategory(Request $request, EntityManagerInterface $entityManager, Security $security)
    {

        $vod = new Category();
        $form = $this->createForm(CategType::class, $vod);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($vod);
            $entityManager->flush();
            return $this->redirectToRoute('main');
        }
        return $this->render('admin/add_category.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/category/edit/{id}", name="categ_edit")
     * @param Category $category
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(Category $category, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(CategType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirectToRoute('list_category');
        }
        return $this->render('user/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/category/remove/{id}", name="categ_remove")
     * @param Category $categ
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function remove(Category $categ, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($categ);
        $entityManager->flush();
        $this->addFlash('notice', 'Your changes were saved!');
        return $this->redirectToRoute('list_category');
    }
}
