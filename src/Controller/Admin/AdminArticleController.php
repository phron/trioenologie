<?php

namespace App\Controller\Admin;

use DateTimeImmutable;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/article', name:'admin_article_')]
class AdminArticleController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function indexArticles(ArticleRepository $articleRepository): Response
    {
        return $this->render('admin/article/articles.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function newArticle(
        Request $request, 
        ArticleRepository $articleRepository, 
        EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article, ['add' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setCreatedAt(new DateTimeImmutable('now'));         
            $imgArticleFile = $form->get('img')->getData();


            if($imgArticleFile)
            {
                $nomImgArticle = date('YmdHis') . "-" . uniqid() . "." . $imgArticleFile->getClientOriginalExtension();

                $imgArticleFile->move(
                    $this->getParameter('articles_directory'),
                    $nomImgArticle
                );

                $article->setImg($nomImgArticle);
            }

            $entityManager->persist($article);
            $entityManager->flush();
            $articleRepository->add($article, true);

            return $this->redirectToRoute('admin_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/article/newArticle.html.twig', [
            'article' => $article,
            'formArticle' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function showArticle(Article $article): Response
    {
        return $this->render('admin/article/showArticle.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function editArticle(
        Request $request, 
        Article $article, 
        ArticleRepository $articleRepository, 
        EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleType::class, $article, ['update' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imgArticleFile = $form->get('imgUpdate')->getData();


            if($imgArticleFile)
            {
                $nomImgArticle = date('YmdHis') . "-" . uniqid() . "." . $imgArticleFile->getClientOriginalExtension();

                $imgArticleFile->move(
                    $this->getParameter('articles_directory'),
                    $nomImgArticle
                );

                $article->setImg($nomImgArticle);
            }

            $entityManager->persist($article);
            $entityManager->flush();
            $articleRepository->add($article, true);

            return $this->redirectToRoute('admin_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/article/editArticle.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function deleteArticle(
        Request $request, 
        Article $article, 
        ArticleRepository $articleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $articleRepository->remove($article, true);
        }

        return $this->redirectToRoute('admin_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
