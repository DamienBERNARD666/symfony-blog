<?php

namespace App\Controller;

use App\Entity\Articles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    /**
     * @Route("/sitemap.xml", name="sitemap", defaults={"_format"="xml"})
     */
    public function index(Request $request)
    {
        $hostname = $request->getSchemeAndHttpHost();
        $urls = [];

        $urls[] = ['loc' => $this->generateUrl('main')];
        $urls[] = ['loc' => $this->generateUrl('app_login')];
        $urls[] = ['loc' => $this->generateUrl('app_register')];

        foreach ($this->getDoctrine()->getRepository(Articles::class)->findAll() as $article) {
            $images = [
                'loc' => '/uploads/images/featured' . $article->getFeaturedImage(),
                'title' => $article->getTitre()
            ];
            $urls[] = [
                'loc' => $this->generateUrl('actualites_article', [
                    'slug' => $article->getSlug()
                ]),
                'image' => $images,
                'lastmod' => $article->getUpdatedAt()->format('Y-m-d')
            ];
        }

        // Fabrication de la réponse XML
$response = new Response(
    $this->renderView('sitemap/index.html.twig', ['urls' => $urls,
        'hostname' => $hostname]),
    200
);

// Ajout des entêtes
$response->headers->set('Content-Type', 'text/xml');

        return $response;
    }
}
