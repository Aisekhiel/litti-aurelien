<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class SamplesController extends Controller
{
  /**
   * @Route("/samples.html", name="samples")
   */
  public function indexAction()
  {
    return $this->render('samples.html.twig');
  }
}
