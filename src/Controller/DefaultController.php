<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */ 
    public function index(): Response
    {
        return $this->render('/index.html.twig', [
            'title' => 'Besoin d\'infos sur une série ? On a ce qu\'il te faut !',
         ]);
    }
}