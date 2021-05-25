<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\GoodPlan;
use App\Form\CommentType;
use App\Form\GoodPlanType;
use App\Repository\GoodPlanRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route({
 *     "en": "/good-plans",
 *     "fr": "/bon-plans"
 * })
 */
class GoodPlanController extends AbstractController
{
    /**
     * @Route("/", name="good_plan_index", methods={"GET"})
     */
    public function index(GoodPlanRepository $goodPlanRepository): Response
    {
        return $this->render('pages/good_plan/index.html.twig', [
            'good_plans' => $goodPlanRepository->findAll(),
        ]);
    }

    /**
     * @Route({"en": "/new", "fr": "/ajouter"}, name="good_plan_new", methods={"GET","POST"})
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function new(Request $request): Response
    {
        $goodPlan = new GoodPlan();
        $form = $this->createForm(GoodPlanType::class, $goodPlan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $goodPlan->setAuthor($this->getUser());
            if ($form->get('freeShipping')->getData() == true) {
                $goodPlan->setShippingCost(0);
            }
            $entityManager->persist($goodPlan);
            $entityManager->flush();

            return $this->redirectToRoute('good_plan_index');
        }

        return $this->render('pages/good_plan/new.html.twig', [
            'good_plan' => $goodPlan,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="good_plan_show", methods={"GET", "POST"})
     */
    public function show(Request $request, GoodPlan $goodPlan): Response
    {
        $newComment = new Comment();
        $newComment->setDeal($goodPlan);
        $commentForm = $this->createForm(CommentType::class, $newComment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $newComment->setAuthor($this->getUser());
            $entityManager->persist($newComment);
            $entityManager->flush();
        }

        return $this->render('pages/good_plan/show.html.twig', [
            'good_plan' => $goodPlan,
            'commentForm' => $commentForm->createView(),
        ]);
    }

    /**
     * @Route({"en": "/{id}/edit", "fr": "/{id}/edition"}, name="good_plan_edit", methods={"GET","POST"})
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function edit(Request $request, GoodPlan $goodPlan): Response
    {
        if ($this->getUser()->getId() != $goodPlan->getAuthor()->getId() || in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(GoodPlanType::class, $goodPlan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('good_plan_index');
        }

        return $this->render('pages/good_plan/edit.html.twig', [
            'good_plan' => $goodPlan,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="good_plan_delete", methods={"POST"})
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function delete(Request $request, GoodPlan $goodPlan): Response
    {
        if ($this->getUser()->getId() != $goodPlan->getAuthor()->getId() || in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            throw new AccessDeniedHttpException();
        }

        if ($this->isCsrfTokenValid('delete'.$goodPlan->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($goodPlan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('good_plan_index');
    }
}
