<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
  /**
   * @Route("/", name="homepage")
   */
  public function indexAction()
  {
    $projects = [
      [
        'title'         => "Kakeibo",
        'category_name' => "Site web",
        'date'          => "En cours",
        'tags'          => ['Bootstrap', 'HTML/CSS', 'PHP', 'MySQL'],
        'thumb_url'     => 'images/projects/thumb-kakeibo.png',
        'desc'          => "Ce site a été réalisé avec un modèle MVC maison,
          il permet de mieux visualiser et gérer ses dépenses.<br>
          C'est une sorte de livret de compte en ligne, avec génération de graphiques
          trié par catégorie."
      ],
      [
        'title'         => "Everyday Electro",
        'category_name' => "Site web",
        'date'          => "Février 2012",
        'tags'          => ['HTML/CSS', 'PHP', 'Référencement'],
        'link'          => 'http://everydayelectro.com',
        'thumb_url'     => 'images/projects/thumb-everyday-electro.png',
        'desc'          => "Everyday Electro est un projet réalisé lors
          de mon année en Licence MIW, le but étant de faire du référencement sur
          un thème libre. J'ai donc créé un site web publiant des musiques
          quotidiennement, en référençant chaque artiste présent sur le site."
      ],
      [
        'title'         => "Scrat: Aider le à garder son gland",
        'category_name' => "Application Flash",
        'date'          => "Janvier 2012",
        'tags'          => ['Flash', 'Photoshop', 'HTML/CSS', 'PHP', 'MySQL'],
        'thumb_url'     => 'images/projects/thumb-scrat.png',
        'link'          => 'http://scrat.fr',
        'desc'          => "
          <p>
            Cette application flash a été réalisée lors d'un projet
            multimédia, le but est d'aider Scrat à conserver le plus de
            glands possible.
          </p>
          <p>
            En plus d'avoir réalisé plusieurs jeux en flash ainsi qu'un
            mode histoire, nous avons mis en place un système de scoring en
            PHP/MySQL afin de faire concurrencer les joueurs en ligne.
          </p>
          <p>
            J'ai aussi créé plusieurs éléments graphiques de l'application
            Flash et du site à l'aide de Photoshop.
          </p>"
      ],
    ];

    // Plugins / Tools
    $plugins = [
      [
        'name'          => "FlexSlider : Script JQuery",
        'link'          => 'https://codepen.io/khiel/pen/NdXXLa',
        'desc'          => "
          <p>
            <strong>FlexSlider</strong> est une <strong>extension jQuery</strong>
            permettant de réaliser des galeries.<br>
            J'ai séparé au mieux la gestion des animations et le style des
            galeries, tout en offrant la possibilité de définir de nouvelles
            animations grâce à des callbacks.
          </p>
          <p>
            En plus de ça, la gestion des évènements \"Touch\" est complètement
            prise en charge et indépendante des évènements classiques, permettant
            ainsi une meilleure gestion des animations.
          </p>
          <p>
            Par défaut, il existe 2 animations simples, un fondu et un effet de
            \"glisse\" des images.
          </p>"
      ],
      [
        'name'          => "SCSS Glow",
        'link'          => 'https://codepen.io/khiel/pen/bBqJvK',
        'desc'          => "
          <p>
            Cet effet de lumière a été réalisé pour un client lors de l'intégration
            d'une charte, je l'ai ensuite récupéré et modifié pour en faire une mixin SCSS.<br>
            De cette manière il est possible de ré-utiliser facilement cet effet, et
            de changer plusieurs de ses propriétés, taille, couleur, ...
          </p>
          <p>
            Cela m'a aussi permit de tester l'attribut <code>currentColor</code>
            qui permet d'hériter de la couleur de texte parente pour l'utiliser
            en tant que couleur de fond par exemple, et ainsi réaliser plus simplement une animation CSS.
          </p>
          "
      ],
    ];

    return $this->render(
      'homepage/index.html.twig',
      [
        'projects'  => $projects,
        'plugins'   => $plugins,
      ]
    );
  }


  /**
   * @Route("/contact-me", name="contact_me")
   */
  public function contactMeAction(Request $request) {
    // is it an Ajax request ?
    $isAjax = $request -> isXmlHttpRequest();

    if(!$isAjax) {
      // do a permanent - 301 redirect
      return $this->redirectToRoute('homepage', array(), 301);
    } else {
      $name     = $request -> get('name');
      $email    = $request -> get('email');
      $message  = $request -> get('message');
      $phone    = $request -> get('phone');
      $website  = $request -> get('website');

      // dump($data);
      // $response = new Response(json_encode($name));
      // $response -> headers -> set('Content-Type', 'application/json');

      $response = [
        'errors' => []
      ];

      // NAME validation
      if(empty($name))
        $response['errors'][] = 'Le nom est obligatoire';

      // EMAIL validation
      if(empty($email)) {
        $response['errors'][] = 'L\'adresse email est obligatoire';
      } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['errors'][] = 'L\'adresse email fournit, \''.$email.'\' n\'est pas au bon format (ex: dupond@dupont.fr)';
      }

      // MESSAGE validation
      if(empty($message))
        $response['errors'][] = 'Le message est obligatoire';

      if(empty($response['errors'])) {
        // MAILING
        $message = \Swift_Message::newInstance()
          ->setSubject("[litti-aurelien.fr] $name vous a envoyé un message !")
          ->setFrom([$email => $name])
          ->setTo('litti.aurelien@gmail.com')
          ->setBody(
            $this -> renderView(
              'emails/contact-message.html.twig',
              array(
                'name'    => $name,
                'email'   => $email,
                'message' => $message,
                'phone'   => $phone,
                'website' => $website,
                // User informations
                'user_agent'  => $_SERVER['HTTP_USER_AGENT'],
                'remote_addr' => $_SERVER['REMOTE_ADDR']
              )
            ),
            'text/html');

        $this -> get('mailer') -> send($message);
      }

      return $this -> json($response);
    }
  }
}
