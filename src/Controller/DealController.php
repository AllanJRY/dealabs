<?php

namespace App\Controller;

use App\Entity\Deal;
use App\Entity\Rating;
use App\Entity\User;
use App\Event\DealRatedEvent;
use App\Repository\UserRepository;
use App\Service\Mailer;
use App\Service\RatingService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;

class DealController extends AbstractController
{
    /**
     * @var RatingService
     */
    private $ratingService;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * DealController constructor.
     * @param RatingService $ratingService
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param Mailer $mailer
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(RatingService $ratingService, UserRepository $userRepository, EntityManagerInterface $entityManager, Mailer $mailer, EventDispatcherInterface $eventDispatcher)
    {
        $this->ratingService = $ratingService;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/deals/rate/{id}", name="rate_deal")
     * @param Request $request
     * @param Deal $deal
     * @return JsonResponse
     */
    public function rateDeal(Request $request, Deal $deal): JsonResponse
    {
        $user = $this->userRepository->find($request->request->get('userID'));
        $value = intval($request->request->get('value'));

        $userRating = $this->ratingService->tryFindUserRatingOfDeal($user, $deal);
        if($userRating instanceof Rating) {
            $this->ratingService->updateRatingValue($userRating, $value);
        } else {
            $this->ratingService->createRating($user, $deal, $value);
            $this->eventDispatcher->dispatch(new DealRatedEvent($deal, $user), DealRatedEvent::NAME);
        }

        $hotValue = 0;
        foreach ($deal->getRatings() as $dealRating) {
            $hotValue += $dealRating->getValue();
        }
        return new JsonResponse(["hotValue" => $hotValue], 200);
    }

    /**
     * @Route("/deals/{id}/expired", name="set_expired_deal")
     * @IsGranted("ROLE_ADMIN")
     * @param Deal $deal
     * @return Response
     */
    public function setDealExpired(Deal $deal): Response
    {
        if (!$deal->getExpired()) {
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

        if (!$deal->getExpired() && $user != null) {
            try {
                $this->mailer->send('mail/deal/expired_report.html.twig',
                    [
                        'deal' => $deal,
                    ],
                    $this->getParameter('admin_mail'),
                    $user->getEmail(),
                );
            } catch (TransportExceptionInterface | LoaderError | SyntaxError | \Throwable $e) {
            }

            return new JsonResponse(null, 200);
        }

        return new JsonResponse(null, 201);
    }

    /**
     * @Route("/deals/{id}/report", name="report_deal")
     * @param Request $request
     * @param Deal $deal
     * @return JsonResponse
     */
    public function reportDeal(Request $request, Deal $deal): JsonResponse
    {
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->find($request->request->get('userID'));

        if (!$deal->getExpired() && $user != null) {
            try {
                $this->mailer->send('mail/deal/report.html.twig',
                    [
                        'deal' => $deal,
                    ],
                    $this->getParameter('admin_mail'),
                    $user->getEmail(),
                );
            } catch (TransportExceptionInterface | LoaderError | SyntaxError | \Throwable $e) {
            }

            return new JsonResponse(null, 200);
        }

        return new JsonResponse(null, 201);
    }

    /**
     * @Route("/deals/{id}/save", name="save_deal")
     * @param Request $request
     * @param Deal $deal
     * @return JsonResponse
     */
    public function saveDeal(Request $request, Deal $deal): JsonResponse
    {
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->find($request->request->get('userID'));

        // TODO check si le deal a déjà été save
        if (!$deal->getExpired() && $user != null && !$user->isClosed()) {
            $user->addSavedDeal($deal);
            $this->entityManager->flush();

            return new JsonResponse(['username' => $user->getUsername()], 200);
        }

        return new JsonResponse(null, 201);
    }
}
