<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Deal;
use App\Form\DealCommentType;
use App\Form\DealType;
use App\Repository\CommentRepository;
use App\Repository\DealRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/deal')]
class DealController extends AbstractController
{
    #[Route('/', name: 'deal_index', methods: ['GET'])]
    public function index(DealRepository $dealRepository): Response
    {
        return $this->render('deal/index.html.twig', [
            'deals' => $dealRepository->findAll(),
            'hotDeals' => $dealRepository->get5HotestDeal(),
            'currentUser' => $this->getUser()
        ]);
    }

    #[Route('/report/{id}', name: 'deal_report')]
    public function report(Request $request, Deal $deal, MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from($this->getUser()->getEmail())
            ->to('support@dealabs.com')
            ->subject('Deal Report')
            ->text('The user ' . $this->getUser()->getUsername() . ' report the deal ' . $deal->getTitle() . ' with the following id : ' . $deal->getId());

        $mailer->send($email);
        $this->addFlash('success', 'Deal reported successfully');
        return $this->redirectToRoute('deal_index');
    }

    #[Route('/hotDeal', name: 'deal_hot', methods: ['GET'])]
    public function hotDeal(DealRepository $dealRepository): Response
    {
        return $this->render('deal/index.html.twig', [
            'deals' => $dealRepository->getAllHotDeal(),
        ]);
    }

    #[Route('/new', name: 'deal_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DealRepository $dealRepository): Response
    {
        $deal = new Deal();
        $deal->setCreator($this->getUser());
        $form = $this->createForm(DealType::class, $deal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dealRepository->save($deal, true);

            return $this->redirectToRoute('deal_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('deal/new.html.twig', [
            'deal' => $deal,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/increment', name: 'deal_increment', methods: ['GET', 'POST'])]
    public function increment(Request $request, Deal $deal, DealRepository $dealRepository): Response
    {
        $deal->setNote($deal->getNote() + 1);
        $deal->addVotedUser( $this->getUser());
        $dealRepository->save($deal, true);
        return $this->json(['note' => $deal->getNote()]);
    }

    #[Route('/{id}/decrement', name: 'deal_decrement', methods: ['GET', 'POST'])]
    public function decrement(Request $request, Deal $deal, DealRepository $dealRepository): Response
    {
        $deal->setNote($deal->getNote() - 1);
        $deal->removeVotedUser($this->getUser());
        $dealRepository->save($deal, true);
        return $this->json(['note' => $deal->getNote()]);
    }

    #[Route('/{id}', name: 'deal_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Deal $deal, DealRepository $dealRepository, CommentRepository $commentRepository): Response
    {
        $comment = new Comment();
        $commentForm = $this->createForm(DealCommentType::class, $comment);

        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setDeal($deal);
            $comment->setDate(new \DateTime());
            $comment->setLikes(0);
            $commentRepository->save($comment);
            $dealRepository->save($deal, true);
        }

        return $this->render('deal/show.html.twig', [
            'deal' => $deal,
            'commentForm' => $commentForm,
        ]);
    }

    #[Route('/{id}/edit', name: 'deal_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Deal $deal, DealRepository $dealRepository): Response
    {
        $form = $this->createForm(DealType::class, $deal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dealRepository->save($deal, true);

            return $this->redirectToRoute('deal_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('deal/edit.html.twig', [
            'deal' => $deal,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'deal_delete', methods: ['POST'])]
    public function delete(Request $request, Deal $deal, DealRepository $dealRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$deal->getId(), $request->request->get('_token'))) {
            $dealRepository->remove($deal, true);
        }

        return $this->redirectToRoute('deal_index', [], Response::HTTP_SEE_OTHER);
    }
}
