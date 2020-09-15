<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Repository\ArticlesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/api", name="api_")
 */

class APIController extends AbstractController
{
    /**
     * @Route("/articles/liste", name="liste", methods={"GET"})
     */
    public function getAllArticles(ArticlesRepository $articlesRepo)
    {
       $articles = $articlesRepo->apiFindAll();
       $encoders =  [new JsonEncoder()];
       $normalizers = [new ObjectNormalizer() ];


       $serializer = new Serializer($normalizers, $encoders);

       $json = $serializer->serialize($articles, 'json', [
           'circular_reference_handler' => function($object){
               return $object->getId();
           }
       ]);

       $response = new Response($json);
       $response->headers->set('Content-type', 'application/json');

       return $response;

    }

    /**
     * @Route("/article/{id}", name="lire", methods={"GET"})
     */
    public function getOneArticle(ArticlesRepository $articlesRepo, Request $request)
    {

        $article = $articlesRepo->apiFindOneById($request->get('id'));
        //TODO: Sécuriser les données renvoyés, supprimé les utilisateurs
       $encoders =  [new JsonEncoder()];
       $normalizers = [new ObjectNormalizer() ];


       $serializer = new Serializer($normalizers, $encoders);

       $json = $serializer->serialize($article, 'json', [
           'circular_reference_handler' => function($object){
               return $object->getId();
           }
       ]);

       $response = new Response($json);
       $response->headers->set('Content-type', 'application/json');

       return $response;

    }
}
