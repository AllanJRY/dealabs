<?php

namespace App\Controller;

use App\Entity\Deal;
use App\Entity\Rating;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Mailer;
use App\Service\RatingService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DealController extends AbstractController
{
    private $ratingService;
    private $userRepository;
    private $entityManager;
    private $mailer;

    /**
     * DealController constructor.
     * @param RatingService $ratingService
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param Mailer $mailer
     */
    public function __construct(RatingService $ratingService, UserRepository $userRepository, EntityManagerInterface $entityManager, Mailer $mailer)
    {
        $this->ratingService = $ratingService;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/deals/rate/{id}", name="rate_deal")
     * @param Request $request
     * @param Deal $deal
     * @return JsonResponse
     */
    public function index(Request $request, Deal $deal): JsonResponse
    {
        $user = $this->userRepository->find($request->request->get('userID'));
        $value = intval($request->request->get('value'));

        $userRating = $this->ratingService->tryFindUserRatingOfDeal($user, $deal);
        if($userRating instanceof Rating) {
            $this->ratingService->updateRatingValue($userRating, $value);
        } else {
            $this->ratingService->createRating($user, $deal, $value);
        }

        // TODO send back new hot value
        return new JsonResponse(null, 204);
    }

    /**
     * @Route("/deals/{id}/expired", name="set_expired_deal")
     * @IsGranted("ROLE_ADMIN")
     * @param Deal $deal
     * @return Response
     */
    public function setDealExpired(Deal $deal): Response
    {
        if(!$deal->getExpired()) {
            $deal->setExpired(true);
        }

        $this->entityManager->flush();

        return $this->render('pages/deal/set_expired.html.twig', [
            "deal" => $deal,
        ]);
    }

    /**
     * @Route("/deals/{id}/report-expired", name="report_expired_deal")
     * @param Request $request
     * @param Deal $deal
     * @return JsonResponse
     */
    public function reportExpiredDeal(Request $request, Deal $deal): JsonResponse
    {
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->find($request->request->get('userID'));

        if(!$deal->getExpired() && $user != null) {
            $this->mailer->send('mail/deal/expired_report.html.twig',
                [
                    'deal' => $deal,
                ],
                $this->getParameter('admin_mail'),
                $user->getEmail(),
            );

            return new JsonResponse(null, 200);
        }

        return new JsonResponse(null, 201);
    }

    /**
     * @Route("/deals/{id}/report", name="report_deal")
     * @param Deal $deal
     */
    public function reportDeal(Request $request, Deal $deal) {
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->find($request->request->get('userID'));

        if(!$deal->getExpired() && $user != null) {
            $this->mailer->send('mail/deal/report.html.twig',
                [
                    'deal' => $deal,
                ],
                $this->getParameter('admin_mail'),
                $user->getEmail(),
            );

            return new JsonResponse(null, 200);
        }

        return new JsonResponse(null, 201);
    }
}
