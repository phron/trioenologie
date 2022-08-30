<?php

namespace App\Controller\Admin;

use App\Entity\Status;
use App\Form\Admin\AdminStatusType;
use App\Repository\StatusRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/status', name:'admin_status_')]
class AdminStatusController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function indexStatus(StatusRepository $statusRepository): Response
    {
        return $this->render('admin/status/status.html.twig', [
            'statuses' => $statusRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function newStatus(
        Request $request, 
        StatusRepository $statusRepository): Response
    {
        $status = new Status();
        $form = $this->createForm(AdminStatusType::class, $status);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $statusRepository->add($status, true);

            return $this->redirectToRoute('admin_status_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/status/newStatus.html.twig', [
            'status' => $status,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function showStatus(Status $status): Response
    {
        return $this->render('admin/status/showStatus.html.twig', [
            'status' => $status,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function editStatus(
        Request $request, 
        Status $status, 
        StatusRepository $statusRepository): Response
    {
        $form = $this->createForm(AdminStatusType::class, $status);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $statusRepository->add($status, true);

            return $this->redirectToRoute('admin_status_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/status/editStatus.html.twig', [
            'status' => $status,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function deleteStatus(
        Request $request, 
        Status $status, 
        StatusRepository $statusRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$status->getId(), $request->request->get('_token'))) {
            $statusRepository->remove($status, true);
        }

        return $this->redirectToRoute('admin_status_index', [], Response::HTTP_SEE_OTHER);
    }
}
