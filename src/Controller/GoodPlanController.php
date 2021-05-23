<?php

namespace App\Controller;

use App\Entity\GoodPlan;
use App\Form\GoodPlanType;
use App\Repository\GoodPlanRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/{id}", name="good_plan_show", methods={"GET"})
     */
    public function show(GoodPlan $goodPlan): Response
    {
        return $this->render('pages/good_plan/show.html.twig', [
            'good_plan' => $goodPlan,
        ]);
    }

    /**
     * @Route({"en": "/{id}/edit", "fr": "/{id}/edition"}, name="good_plan_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, GoodPlan $goodPlan): Response
    {
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
     * @Route("/{id}", name="good_plan_delete", methods={"POST"})
     */
    public function delete(Request $request, GoodPlan $goodPlan): Response
    {
        if ($this->isCsrfTokenValid('delete'.$goodPlan->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($goodPlan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('good_plan_index');
    }
}
