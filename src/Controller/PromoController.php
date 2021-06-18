<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\File;
use App\Entity\Promo;
use App\Form\CommentType;
use App\Form\PromoType;
use App\Repository\PromoRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route({
 *     "en": "/promos",
 *     "fr": "/promotions"
 * })
 */
class PromoController extends AbstractController
{
    /**
     * @Route("/", name="promo_index", methods={"GET"})
     */
    public function index(PromoRepository $promoRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $data = $promoRepository->findAllOrderByCreatedAtDesc();
        $promos = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/promo/index.html.twig', [
            'promos' => $promos,
        ]);
    }

    /**
     * @Route("/hot", name="hot_promo_index", methods={"GET"})
     */
    public function indexHot(PromoRepository $promoRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $data = $promoRepository->findAllHotOrderByRatingDesc();
        $promos = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/promo/index.html.twig', [
            'promos' => $promos,
        ]);
    }

    /**
     * @Route({"en": "/new", "fr": "/ajouter"}, name="promo_new", methods={"GET","POST"})
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function new(Request $request): Response
    {
        $promo = new Promo();
        $form = $this->createForm(PromoType::class, $promo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $promo->setAuthor($this->getUser());
            $pictureFile = $form->get('picture')->getData();
            if ($pictureFile) {
                $file = new File();
                $file->setFile($pictureFile);
                $file->preUpload();
                $entityManager->persist($file);
                $entityManager->flush();
                $file->upload();
                $promo->setPicture($file);
            }

            $entityManager->persist($promo);
            $entityManager->flush();

            return $this->redirectToRoute('promo_index');
        }

        return $this->render('pages/promo/new.html.twig', [
            'promo' => $promo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="promo_show", methods={"GET","POST"})
     */
    public function show(Request $request, Promo $promo): Response
    {
        $newComment = new Comment();
        $newComment->setDeal($promo);
        $commentForm = $this->createForm(CommentType::class, $newComment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $newComment->setAuthor($this->getUser());
            $entityManager->persist($newComment);
            $entityManager->flush();
        }

        return $this->render('pages/promo/show.html.twig', [
            'deal' => $promo,
            'commentForm' => $commentForm->createView(),
        ]);
    }

    /**
     * @Route({"en": "/{id}/edit", "fr": "/{id}/edition"}, name="promo_edit", methods={"GET","POST"})
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function edit(Request $request, Promo $promo): Response
    {
        if ($this->getUser()->getId() != $promo->getAuthor()->getId() || in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(PromoType::class, $promo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('promo_index');
        }

        return $this->render('pages/promo/edit.html.twig', [
            'promo' => $promo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="promo_delete", methods={"POST"})
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function delete(Request $request, Promo $promo): Response
    {
        if ($this->getUser()->getId() != $promo->getAuthor()->getId() || in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            throw new AccessDeniedHttpException();
        }

        if ($this->isCsrfTokenValid('delete'.$promo->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($promo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('promo_index');
    }
}
