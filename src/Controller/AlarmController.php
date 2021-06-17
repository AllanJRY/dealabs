<?php

namespace App\Controller;

use App\Entity\Alarm;
use App\Form\AlarmType;
use App\Repository\AlarmRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AlarmController
 * @package App\Controller
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
 * @Route("/alarm")
 */
class AlarmController extends AbstractController
{
    /**
     * @Route("/", name="alarm_index", methods={"GET","POST"})
     * @param AlarmRepository $alarmRepository
     * @param Request $request
     * @return Response
     */
    public function index(AlarmRepository $alarmRepository, Request $request): Response
    {
        $alarm = new Alarm();
        $form = $this->createForm(AlarmType::class, $alarm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $alarm->setUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($alarm);
            $entityManager->flush();

            return $this->redirectToRoute('alarm_index');
        }

        return $this->render('pages/alarm/index.html.twig', [
            'alarm' => $alarm,
            'form' => $form->createView(),
            'alarms' => $alarmRepository->findBy(['user' => $this->getUser()]),
        ]);
    }

    /**
     * @Route("/new", name="alarm_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $alarm = new Alarm();
        $form = $this->createForm(AlarmType::class, $alarm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($alarm);
            $entityManager->flush();

            return $this->redirectToRoute('alarm_index');
        }

        return $this->render('pages/alarm/new.html.twig', [
            'alarm' => $alarm,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="alarm_show", methods={"GET"})
     */
    public function show(Alarm $alarm): Response
    {
        return $this->render('pages/alarm/show.html.twig', [
            'alarm' => $alarm,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="alarm_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Alarm $alarm): Response
    {
        $form = $this->createForm(AlarmType::class, $alarm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('alarm_index');
        }

        return $this->render('pages/alarm/edit.html.twig', [
            'alarm' => $alarm,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="alarm_delete", methods={"POST"})
     */
    public function delete(Request $request, Alarm $alarm): Response
    {
        if ($this->isCsrfTokenValid('delete' . $alarm->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($alarm);
            $entityManager->flush();
        }

        return $this->redirectToRoute('alarm_index');
    }
}
