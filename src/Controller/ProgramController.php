<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;
use App\Form\ProgramType;
use App\Service\Slugify;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
* @Route("/program/", name="program_")
*/
class ProgramController extends AbstractController
{
    /**
     * Show all rows from Program's entity.
     * 
     * @Route("", name="index")
     */ 
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        return $this->render('program/index.html.twig', ['programs' => $programs]);
    }

    /**
     * The controller for the program add form
     *
     * @Route("new", name="new")
     */
    public function new(Request $request, Slugify $slugify) : Response
    {
        // Create a new Program Object
        $program = new Program();
        // Create the associated Form
        $form = $this->createForm(ProgramType::class, $program);
        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted?
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the entity manager
            $entityManager = $this->getDoctrine()->getManager();
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            // Persists the program
            $entityManager->persist($program);
            // Flush the program
            $entityManager->flush();
            // redirect to the programs page
            return $this->redirectToRoute('program_index');
        }

        // Render the form
        return $this->render('program/new.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    /**
     * @Route("{slug}", name="show", methods={"GET"})
     */ 
    public function show(Program $program): Response
    {
        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['program' => $program]);
        
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $program   . ' found in program\'s table.'
            );
        }

        if (!$seasons) {
            throw $this->createNotFoundException(
                'Season not found for this program.'
            );
        }

        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons
         ]);
    }

    /**
     * @Route("{slug}/season/{seasonId}", name="season_show", methods={"GET"})
     */
    public function showSeason(Program $programId, Season $seasonId): Response
    {
        return $this->render('program/season_show.html.twig', [
            'program' => $programId,
            'season' => $seasonId
        ]);
    }

    /**
     * @Route("{program_slug}/season/{seasonId}/episode/{episode_slug}", name="episode_show", methods={"GET"})
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"program_slug": "slug"}})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episode_slug": "slug"}})
     */
    public function showEpisode(Program $program_slug, Season $seasonId, Episode $episodeId): Response
    {
        return $this->render('/program/episode_show.html.twig', [
            'program' => $program_slug,
            'season' => $seasonId,
            'episode' => $episodeId
        ]);
    }
}