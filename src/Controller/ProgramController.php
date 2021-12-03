<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Episode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\ProgramType;
use Symfony\Component\HttpFoundation\Request;
// use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
    public function new(Request $request) : Response
    {
        // Create a new Program Object
        $program = new Program();
        // Create the associated Form
        $form = $this->createForm(ProgramType::class, $program);
        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted?
        if ($form->isSubmitted()) {
            // Get the entity manager
            $entityManager = $this->getDoctrine()->getManager();
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
     * @Route("{program}", name="show", methods={"GET"}, requirements={"program"="\d+"})
     */ 
    public function show(Program $program): Response
    {
        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['program' => $program]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $program   . ' found int program\'s table.'
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
     * @Route("{programId}/season/{seasonId}", name="season_show", methods={"GET"}, requirements={"programId"="\d+", "seasonId"="\d+"})
     */
    public function showSeason(Program $programId, Season $seasonId): Response
    {
        return $this->render('program/season_show.html.twig', [
            'program' => $programId,
            'season' => $seasonId
        ]);
    }

    /**
     * @Route("{programId}/season/{seasonId}/episode/{episodeId}", name="episode_show")
     */
    public function showEpisode(Program $programId, Season $seasonId, Episode $episodeId): Response
    {
        return $this->render('/program/episode_show.html.twig', [
            'program' => $programId,
            'season' => $seasonId,
            'episode' => $episodeId
        ]);
    }
}