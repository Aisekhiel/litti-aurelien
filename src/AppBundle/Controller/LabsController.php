<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class LabsController extends Controller
{
    /**
     * @Route("/projets-et-experiences/{page}", name="labs_list", requirements={"page": "\d+"})
     */
    public function listAction($page = 1)
    {
        return $this->render('labs/index.html.twig', [
            'page_number' => $page,
        ]);
    }

    /**
     * @Route("/projets-et-experiences/projets/{slug}", name="labs_projects")
     */
    public function projetsAction($slug)
    {
        return $this->render('labs/project.html.twig', [
            'slug' => $slug,
        ]);
    }

    /**
     * @Route("/projets-et-experiences/experiences/{slug}", name="labs_expes")
     */
    public function expesAction($slug)
    {
        return $this->render('labs/expe.html.twig', [
            'slug' => $slug,
        ]);
    }
}
