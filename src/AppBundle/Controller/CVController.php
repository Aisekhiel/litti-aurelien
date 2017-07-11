<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class CVController extends Controller
{
  /**
   * @Route("/curriculum-vitae.html", name="CV")
   */
  public function indexAction()
  {
    return $this->render('CV.html.twig');
  }
}
