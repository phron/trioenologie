<?php 

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Admin\AdminUserType;
use App\Repository\UserRepository;
use App\Repository\ProfileRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/admin/user', name:'admin_user_')]
class AdminUserController extends AbstractController

{

    #[Route('/', name:'index')]
    public function indexUsers(
        UserRepository $userRepository, 
        ProfileRepository $profileRepository): Response
    {
        return $this->render('admin/user/users.html.twig', [
            'users' => $userRepository->findAll(),
            'profiles' => $profileRepository->findAll(),
        ]);
    }   


    #[Route('/{id}', name:'show')]
    public function showUser(User $user): Response
    {
        return $this->render('admin/user/showUser.html.twig', [
            'user' => $user,
        ]);
    }
    

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function editUser(
        Request $request, 
        User $user, 
        UserRepository $userRepository): Response
    {
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);

            return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/editUser.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function deleteUser(
        Request $request, 
        User $user, 
        UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

}