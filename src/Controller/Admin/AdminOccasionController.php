<?php

namespace App\Controller\Admin;

use App\Entity\Picture;
use App\Entity\Occasion;
use App\Form\OccasionType;
use App\Repository\PictureRepository;
use App\Repository\OccasionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/occasion', name:'admin_occasion_')]
class AdminOccasionController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function indexOccasions(OccasionRepository $occasionRepository): Response
    {
        return $this->render('admin/occasion/occasions.html.twig', [
            'occasions' => $occasionRepository->findAll(),
        ]);
    }
    
    #[Route('/calendrier', name:'calendrier', methods: ['GET'])]
    public function calendrier(): Response
    {
        return $this->render('admin/occasion/calendrier.html.twig');
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function newOccasion(
        Request $request, 
        OccasionRepository $occasionRepository,
        EntityManagerInterface $entityManager): Response
    {
        $occasion = new Occasion();
        $form = $this->createForm(OccasionType::class, $occasion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $pictures = $form->get('pictures')->getData();
            
            foreach($pictures as $picture){
                
                $nomPict = date('YmdHis') . "-" . uniqid() . "." . $picture->getClientOriginalExtension();
                               
                $name = $picture->getClientOriginalName();
                
                $picture->move(
                    $this->getParameter('pictures_directory'),
                    $nomPict
                ); 
                
                $pict = new Picture();
                $pict->setTitle($name); 
                $pict->setPictureFile($nomPict);
                
                $occasion -> addPicture($pict);               
            }
            
            $images =  $form->get('savedPictures')->getData();
            
            foreach($images as $image){
                
                $occasion -> addPicture($image);
            }

            $entityManager->persist($occasion);            
            $occasionRepository->add($occasion, true);

            return $this->redirectToRoute('admin_occasion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/occasion/newOccasion.html.twig', [
            'occasion' => $occasion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function showOccasion(Occasion $occasion): Response
    {
        return $this->render('admin/occasion/showOccasion.html.twig', [
            'occasion' => $occasion,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function editOccasion(
        Request $request, 
        Occasion $occasion, 
        OccasionRepository $occasionRepository): Response
    {
        $form = $this->createForm(OccasionType::class, $occasion);
        $form->handleRequest($request);
        $images = $form->get('savedPictures')->getData();

        if ($form->isSubmitted() && $form->isValid()) {

            foreach($images as $image){
                
            $occasion -> addPicture($image);
        }
            $occasionRepository->add($occasion, true);

            return $this->redirectToRoute('admin_occasion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/occasion/editOccasion.html.twig', [
            'occasion' => $occasion,
            'form' => $form,
        ]);
    }

    #[Route('/edit/{occasion_id}/unlinkPicture/{id}', name: 'unlinkPicture', methods: ['GET','DELETE'])]
    public function unlinkPicture(
        $occasion_id, 
        $id, 
        Request $request, 
        OccasionRepository $occasionRepository, 
        PictureRepository $pictureRepository, 
        EntityManagerInterface $entityManager ): Response
    {
        $occasion = $occasionRepository->find($occasion_id);
        $picture = $pictureRepository->find($id);
        $occasion->removePicture($picture);

        $entityManager->persist($occasion);
        $entityManager->flush();
       
        $form = $this->createForm(OccasionType::class, $occasion);
        $form->handleRequest($request);
        
        return $this->renderForm('article/edit.html.twig', [
            'article' => $occasion,
            'form' => $form,
        ]);

    }    

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        Occasion $occasion, 
        OccasionRepository $occasionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$occasion->getId(), $request->request->get('_token'))) {
            $occasionRepository->remove($occasion, true);
        }

        return $this->redirectToRoute('admin_occasion_index', [], Response::HTTP_SEE_OTHER);
    }
}
