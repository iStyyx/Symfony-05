<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use App\Entity\Program;

/**
* @Route("/category/", name="category_")
*/
class CategoryController extends AbstractController
{
    /**
     * Get all catogories
     * 
     * @Route("", name="index")
     */
    public function index(): Response
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * Get all programs from a categorie
     * 
     * @Route("{categoryName}", name="show")
     */
    public function show(string $categoryName): Response
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => $categoryName]);

        if (!$category) {
            throw $this->createNotFoundException(
                'This category doesn\'t exists!'
            );
        }

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category' => $category], ['id' => 'DESC'], 3);

        if (!$programs) {
            throw $this->createNotFoundException(
                'No series found!'
            );
        }

        return $this->render('category/show.html.twig', [
            "category" => $category,
            "programs" => $programs,
        ]);
    }
}
