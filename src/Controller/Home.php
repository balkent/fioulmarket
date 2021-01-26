<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Image\ImageCollector;

class Home extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request, ImageCollector $imageCollector)
    {
        $urls = [
            'http://www.commitstrip.com/en/feed/' => 'RSS',
            'https://newsapi.org/v2/top-headlines?country=us&apiKey=c782db1cd730403f88a544b75dc2d7a0' => 'API',
            'https://www.lemonde.fr/rss/une.xml' => 'RSS',
            'https://www.lemonde.fr/culture/rss_full.xml' => 'RSS',
            'http://newsapi.org/v2/top-headlines?country=us&category=business&apiKey=c782db1cd730403f88a544b75dc2d7a0' => 'API',
        ];
        $images = $imageCollector->handler($urls);

        return $this->render('default/index.html.twig', array('images' => $images));
    }
}
