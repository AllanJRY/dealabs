<?php

namespace App\Controller;

use App\Repository\BadgeRepository;
use App\Repository\DealRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProfilController
 * @package App\Controller
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
 * @Route({
 *     "en": "/my-profile",
 *     "fr": "/mon-profil"
 * })
 */
class ProfilController extends AbstractController
{
    /**
     * @var DealRepository
     */
    private $dealRepository;

    public function __construct(DealRepository $dealRepository)
    {
        $this->dealRepository = $dealRepository;

    }

    /**
     * @Route("/", name="profil_overview")
     */
    public function overview(): Response
    {
//        dump($this->dealRepository->findBestRatingDealByUser($this->getUser()));
        return $this->render('pages/profil/overview.html.twig', []);
    }

    /**
     * @Route("/badges", name="profil_badges")
     */
    public function badges(): Response
    {
        return $this->render('pages/profil/badges.html.twig', [
            'badges' => $this->getUser()->getBadges()
        ]);
    }

    /**
     * @Route("/keyword_alarms", name="profil_keyword_alarms")
     */
    public function keyword_alarms(Request $request)
    {
        $session = $request->getSession()->set('last_time_request_keyword_alarms', new DateTime());
        dump($request->getSession());
//        dump($this->dealRepository->findNewDealByAlarmUserOrderByDateDesc($this->getUser()));
        return $this->render('pages/profil/keyword_alarms.html.twig', [
            'alarms' => $this->dealRepository->findNewDealByAlarmUserOrderByDateDesc($this->getUser())
        ]);
    }


    /**
     * @Route("/settings", name="profil_settings")
     */
    public function settings(): Response
    {
        return $this->render('pages/profil/settings.html.twig', []);
    }

    /**
     * @Route({"en": "/my-deals", "fr": "/mes-deals"}, name="profil_published_deals")
     */
    public function publishedDeals(Request $request, PaginatorInterface $paginator): Response
    {
        $userPublishedDeals = $paginator->paginate(
            $this->getUser()->getDeals(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/profil/published_deals.html.twig', [
            "user_published_deals" => $userPublishedDeals,
        ]);
    }

    /**
     * @Route({"en": "/my-saved-deals", "fr": "/mes-deals-sauvegarde"}, name="profil_saved_deals")
     */
    public function savedDeals(Request $request, PaginatorInterface $paginator): Response
    {
        $userSavedDeals = $paginator->paginate(
            $this->getUser()->getSavedDeals(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/profil/saved_deals.html.twig', [
            "user_saved_deals" => $userSavedDeals,
        ]);
    }
}
