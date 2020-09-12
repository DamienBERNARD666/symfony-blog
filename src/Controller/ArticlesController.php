<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Commentaires;
use App\Form\AjoutArticleFormType;
use App\Form\CommentaireFormType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class ArticlesController
 * @package App\Controller
 * @Route ("/actualites", name="actualites_")
 */

class ArticlesController extends AbstractController
{
    /**
     * @Route("/", name="articles")
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {
        $donnees = $this->getDoctrine()->getRepository(Articles::class)->findBy([],['create_at' => 'desc']);
        
        $articles = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            6
        );
        
        //dd($articles);
        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
        ]);
    }
    
    /**
     * @IsGranted("ROLE_EDITOR")
     * @Route("/article/new", name="ajout_article")
     */
    public function ajoutArticle(Request $request){
        $article = new Articles();
        $from = $this->createForm(AjoutArticleFormType::class, $article);

        $from->handleRequest($request);

        if($from->isSubmitted() && $from->isValid()) {
            $article->setUsers($this->getUser());

            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->persist($article);
            $doctrine->flush();

            $this->addFlash('message', 'Votre article a été publié');

            return $this->redirectToRoute('actualites_articles');
        }

        return $this->render('articles/ajout.html.twig', [
            'articleForm' => $from->createView()
        ]);
    }
    
    /**
     * @Route("/{slug}", name="article")
     */
    public function article($slug, Request $request) {
        $article= $this->getDoctrine()->getRepository(Articles::class)->findOneBy(['slug' => $slug]);
        if(!$article) {
            throw $this->createNotFoundException('L\'article n\'existe pas');
        }

        $commentaires = new Commentaires();
        $form = $this->createForm(CommentaireFormType::class, $commentaires);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

            $commentaires->setArticles($article);
            $commentaires->setCreatedAt(new DateTime('now'));

            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->persist($commentaires);
            $doctrine->flush();
        }


        return $this->render('articles/article.html.twig', [
            'article' => $article,
            'formComment' => $form->createView()
        ]
        
    );

    }

}
