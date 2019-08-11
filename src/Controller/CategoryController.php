<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
}
