<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('app/index.html.twig');
    }

    #[Route('/blog', name:"blog")]
    public function blog(ArticleRepository $repo) : Response
    {
        $articles = $repo->findAll();
        return $this->render('app/blog.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route("/blog/show/{id}", name: "blog_show")]
    public function show(EntityManagerInterface $manager, Request $request, Article $article =null) :Response
    {
        if($article == null)
        {
            return $this->redirectToRoute('home');
        }

    $comment = new Comment;
    if($this->getUser())
    {
        $user = $this->getUser();
    }

    $form = $this->createForm(CommentType::class, $comment);

    $form ->handleRequest($request);

    if($form->isSubmitted() && $form->isValid())
    {
        $comment->setCreatedAt(new \DateTime)
                ->setArticle($article)
                ->setUser($user);
        $manager->persist($comment);
        $manager->flush();
        return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);

    }

        return $this->render('app/show.html.twig', [
            'article' => $article,
            'commentForm' => $form
        ]);
    }

    #[Route('/blog/article/new', name:'new_article')]
    #[Route('/blog/article/edit/{id}', name:'edit_article')]
    public function formArticle(Request $globals, EntityManagerInterface $manager, Article $article = null)
    {
        if($article ==null):
            $article = new Article;
        endif;


        $form= $this->createForm(ArticleType::class, $article );

        $form->handleRequest($globals);



        //dump($article);

        if($form->isSubmitted() && $form->isValid())
        {
            $article->setCreatedAt(new \DateTime);
            $manager->persist($article);
            $manager->flush();
           
            return $this->redirectToRoute('blog');
        }

        return $this->render("app/form.html.twig", [
            "formArticle" => $form,
            "editMode" => $article->getId() !== null
        ]);
    }
}
