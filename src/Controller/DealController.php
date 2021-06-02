<?php

namespace App\Controller;

use App\Entity\Deal;
use App\Entity\Rating;
use App\Repository\UserRepository;
use App\Service\RatingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DealController extends AbstractController
{
    private $ratingService;
    private $userRepository;

    /**
     * DealController constructor.
     * @param EntityManagerInterface $entityManager
     * @param RatingService $ratingService
     * @param UserRepository $userRepository
     */
    public function __construct(RatingService $ratingService, UserRepository $userRepository)
    {
        $this->ratingService = $ratingService;
        $this->userRepository = $userRepository;
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

        return new JsonResponse(null, 204);
    }
}
