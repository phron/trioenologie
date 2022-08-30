<?php

namespace App\Controller\Admin;

use App\Entity\Picture;
use App\Form\PictureType;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/picture', name:'admin_picture_')]
class AdminPictureController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function indexPicture(PictureRepository $pictureRepository): Response
    {
        return $this->render('admin/picture/pictures.html.twig', [
            'pictures' => $pictureRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function newPicture(
        Request $request, 
        PictureRepository $pictureRepository,
        EntityManagerInterface $entityManager): Response
    {
        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture, ['add'=>true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on récupère la valeur du champ d'upload (pictureFile)
            $pictures = $form->get('pictureFile')->getData();          
            $message = "";
            $dup=[];

            // pour chacque image ($pict) dans le tableau $pictures
            foreach($pictures as $pict){                                    
                $picture = new Picture();
                // on attribue un nom de fichier unique à l'image téléchargée
                $nomPict = date('YmdHis') . "-" . uniqid() . "." . $pict->getClientOriginalExtension();                
                // on récupère le nom de fichier original de l'image
                $name = $pict->getClientOriginalName();
                // affecte le nom de fichier calculé à la propriété 'pictureFile' de l'entité Picture
                $picture->setPictureFile($nomPict);
                //on récupère le nom de fichier original de l'image
                $name = $pict->getClientOriginalName();
        
                // on vérifie qu'une image avec ce nom n'existe pas déjà  
                // s'il existe une image avec le même titre dans la bdd
                if ($pictureRepository->findOneByTitle($name)) {
                    // on stoppe le traitement pour cette image et on repart en haut de la boucle pour l'itération suivante
                    continue;
                }else {
                    // on l'affecte à la propriété title
                    $picture->setTitle($name);                    
                    // on enregistre en bdd (les infos de l'image)
                    $entityManager->persist($picture);
                    $entityManager->flush();
                    
                    // on enregistre l'image dans le répertoire uploads/pictures (image physique)
                    $pict->move(
                        $this->getParameter('pictures_directory'),
                        $nomPict
                    ); // EO move     
                    
                    $pictureRepository->add($picture, true);
                }                    
            }  //EO foreach
           
            return $this->redirectToRoute('admin_picture_index', [], Response::HTTP_SEE_OTHER);

        }// EO if form submitted

        return $this->renderForm('admin/picture/newPicture.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function showPicture(Picture $picture): Response
    {
        return $this->render('admin/picture/showPicture.html.twig', [
            'picture' => $picture,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function editPicture(
        Request $request, 
        Picture $picture, 
        PictureRepository $pictureRepository): Response
    {
        $form = $this->createForm(PictureType::class, $picture, ['edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureRepository->add($picture, true);

            return $this->redirectToRoute('admin_picture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/picture/editPicture.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function deletePicture(
        Request $request, 
        Picture $picture, 
        PictureRepository $pictureRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$picture->getId(), $request->request->get('_token'))) {
            $pictureRepository->remove($picture, true);
        }

        return $this->redirectToRoute('admin_picture_index', [], Response::HTTP_SEE_OTHER);
    }
}
