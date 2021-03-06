<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\File;
use App\Entity\GoodPlan;
use App\Event\CommentPublishedEvent;
use App\Event\DealCreatedEvent;
use App\Form\CommentType;
use App\Form\GoodPlanType;
use App\Repository\GoodPlanRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @Route({
 *     "en": "/good-plans",
 *     "fr": "/bon-plans"
 * })
 */
class GoodPlanController extends AbstractController
{

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * GoodPlanController constructor.
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }


    /**
     * @Route("/", name="good_plan_index", methods={"GET"})
     */
    public function index(GoodPlanRepository $goodPlanRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $data = $goodPlanRepository->findAllOrderByCreatedAtDesc();
        $good_plans = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/good_plan/index.html.twig', [
            'good_plans' => $good_plans,
        ]);
    }

    /**
     * @Route("/hot", name="hot_good_plan_index", methods={"GET"})
     */
    public function indexHot(GoodPlanRepository $goodPlanRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $data = $goodPlanRepository->findAllHotOrderByRatingDesc();
        $good_plans = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/good_plan/index.html.twig', [
            'good_plans' => $good_plans,
        ]);
    }

    /**
     * @Route({"en": "/new", "fr": "/ajouter"}, name="good_plan_new", methods={"GET","POST"})
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function new(Request $request): Response
    {
        if ($this->getUser()->isClosed()) throw new AccessDeniedHttpException();

        $goodPlan = new GoodPlan();
        $form = $this->createForm(GoodPlanType::class, $goodPlan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $goodPlan->setAuthor($this->getUser());
            if ($form->get('freeShipping')->getData() == true) {
                $goodPlan->setShippingCost(0);
            }

            $pictureFile = $form->get('picture')->getData();
            if ($pictureFile) {
                $file = new File();
                $file->setFile($pictureFile);
                $file->preUpload();
                $entityManager->persist($file);
                $entityManager->flush();
                $file->upload();
                $goodPlan->setPicture($file);
            }

            $entityManager->persist($goodPlan);
            $entityManager->flush();
            $this->eventDispatcher->dispatch(new DealCreatedEvent($goodPlan), DealCreatedEvent::NAME);

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
            $this->eventDispatcher->dispatch(new CommentPublishedEvent($newComment), CommentPublishedEvent::NAME);
        }

        return $this->render('pages/good_plan/show.html.twig', [
            'deal' => $goodPlan,
            'commentForm' => $commentForm->createView(),
        ]);
    }

    /**
     * @Route({"en": "/{id}/edit", "fr": "/{id}/edition"}, name="good_plan_edit", methods={"GET","POST"})
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function edit(Request $request, GoodPlan $goodPlan): Response
    {
        if ($this->getUser()->getId() != $goodPlan->getAuthor()->getId() || in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || $this->getUser()->isClosed()) {
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
        if ($this->getUser()->getId() != $goodPlan->getAuthor()->getId() || in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || $this->getUser()->isClosed()) {
            throw new AccessDeniedHttpException();
        }

        if ($this->isCsrfTokenValid('delete' . $goodPlan->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($goodPlan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('good_plan_index');
    }
}
