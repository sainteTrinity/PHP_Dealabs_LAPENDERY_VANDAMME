<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\BadgeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/profile', name: 'app_user_profile', methods: ['GET'])]
    public function show(Security $security, BadgeRepository $badgeRepository): Response
    {
        $user = $security->getUser();
        $badges = $badgeRepository->findAll();

        if(!$user) {
            $this->addFlash('warning', 'Il faut être connecté pour pouvoir accéder à son profil.');
            return $this->redirectToRoute("deal_index");
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'badges' => $badges,
        ]);
    }

    #[Route('/profile/deals', name: 'app_user_profile_deals', methods: ['GET'])]
    public function deals(): Response
    {
        /* @var User $user */
        $user = $this->getUser();

        if(!$user) {
            $this->addFlash('warning', 'Il faut être connecté pour pouvoir accéder à son profil.');
            return $this->redirectToRoute("deal_index");
        }

        return $this->render('user/user_deals.html.twig', [
            'deals' => $user->getDeals(),
            'user' => $user,
        ]);
    }

    #[Route('/profile/likedDeals', name: 'app_user_profile_likes', methods: ['GET'])]
    public function likedDeals(): Response
    {
        /* @var User $user */
        $user = $this->getUser();

        if(!$user) {
            $this->addFlash('warning', 'Il faut être connecté pour pouvoir accéder à son profil.');
            return $this->redirectToRoute("deal_index");
        }

        return $this->render('user/liked_deals.html.twig', [
            'deals' => $user->getLikedDeals(),
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->remove('password');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_edit', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
