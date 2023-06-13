<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/admin/article', name: 'admin_article')]
    public function adminArticle(ArticleRepository $repo, EntityManagerInterface $manager)
    {
        $colonnes = $manager->getClassMetadata(Article::class)->getFieldNames();

        // dd($colonnes);
        $articles = $repo->findAll();
        return $this->render('admin/gestionArticle.html.twig', [
            "colonnes" => $colonnes,
            "articles" => $articles
        ]);
    }

    #[Route('/admin/article/edit/{id}', name: "admin_article_edit")]
    #[Route('/admin/article/new', name: "admin_article_new")]
    public function formArticle(Request $request, EntityManagerInterface $manager, Article $article=null): Response
    {
        if($article == null )
        {
            $article = new Article;
        }
        

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $article->setCreatedAt(new \DateTime);
            $manager->persist($article);
            $manager->flush();
            $this->addFlash('success', "L'article a bien été enregistré");
            return $this->redirectToRoute('admin_article');
        }
        return $this->render('admin/formArticle.html.twig', [
            'form' => $form, 
            'editMode' => $article->getId()!=null
        ]);
    }

    #[Route('/admin/article/delete/{id}', name:"admin_article_delete")]
    public function deleteArticle(Article $article, EntityManagerInterface $manager)
    {
        $manager->remove($article);
        $manager->flush();
        $this->addFlash('success', "l'article a bien été supprimé");
        return $this->redirectToRoute('admin_article');
    }
}
