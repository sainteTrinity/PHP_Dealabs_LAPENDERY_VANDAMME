<?php

namespace App\Controller;

use App\Entity\PromoCode;
use App\Form\PromoCodeType;
use App\Repository\PromoCodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/promo_code')]
class PromoCodeController extends AbstractController
{
    #[Route('/', name: 'promo_code_index', methods: ['GET'])]
    public function index(PromoCodeRepository $promoCodeRepository): Response
    {
        return $this->render('promo_code/index.html.twig', [
            'promo_codes' => $promoCodeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'promo_code_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PromoCodeRepository $promoCodeRepository): Response
    {
        $promoCode = new PromoCode();
        $form = $this->createForm(PromoCodeType::class, $promoCode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $promoCodeRepository->save($promoCode, true);

            return $this->redirectToRoute('promo_code_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('promo_code/new.html.twig', [
            'promo_code' => $promoCode,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'promo_code_show', methods: ['GET'])]
    public function show(PromoCode $promoCode): Response
    {
        return $this->render('promo_code/show.html.twig', [
            'promo_code' => $promoCode,
        ]);
    }

    #[Route('/{id}/edit', name: 'promo_code_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PromoCode $promoCode, PromoCodeRepository $promoCodeRepository): Response
    {
        $form = $this->createForm(PromoCodeType::class, $promoCode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $promoCodeRepository->save($promoCode, true);

            return $this->redirectToRoute('promo_code_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('promo_code/edit.html.twig', [
            'promo_code' => $promoCode,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'promo_code_delete', methods: ['POST'])]
    public function delete(Request $request, PromoCode $promoCode, PromoCodeRepository $promoCodeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$promoCode->getId(), $request->request->get('_token'))) {
            $promoCodeRepository->remove($promoCode, true);
        }

        return $this->redirectToRoute('promo_code_index', [], Response::HTTP_SEE_OTHER);
    }
}
