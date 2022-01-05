<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use App\Entity\Program;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
* @Route("/category/", name="category_")
*/
class CategoryController extends AbstractController
{
    /**
     * Get all categories
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
     * The controller for the category add form
     *
     * @Route("new", name="new")
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request) : Response
    {
        // Create a new Category Object
        $category = new Category();
        // Create the associated Form
        $form = $this->createForm(CategoryType::class, $category);
        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted?
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the entity manager
            $entityManager = $this->getDoctrine()->getManager();
            // Persists the category
            $entityManager->persist($category);
            // Flush the category
            $entityManager->flush();
            // redirect to the categories page
            return $this->redirectToRoute('category_index');
        }

        // Render the form
        return $this->render('category/new.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    /**
     * Get all programs from a category
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
