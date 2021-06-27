<?php

namespace App\Controller;

use App\Entity\Badge;
use App\Entity\Deal;
use App\Entity\User;
use App\Repository\BadgeRepository;
use App\Repository\DealRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }

    /**
     * @Route("/", name="profil_overview")
     */
    public function overview(): Response
    {
        if ($this->getUser()->isClosed()) return $this->redirectToRoute('home');
//        dump($this->dealRepository->findBestRatingDealByUser($this->getUser()));
        return $this->render('pages/profil/index.html.twig', []);
    }

    /**
     * @Route("/badges", name="profil_badges")
     */
    public function badges(): Response
    {
        if ($this->getUser()->isClosed()) return $this->redirectToRoute('home');

        return $this->render('pages/profil/badges.html.twig', [
            'badges' => $this->entityManager->getRepository(Badge::class)->findAll()
        ]);
    }

    /**
     * @Route("/keyword_alarms", name="profil_keyword_alarms")
     */
    public function keyword_alarms(Request $request)
    {
        if ($this->getUser()->isClosed()) return $this->redirectToRoute('home');

        $session = $request->getSession()->set('last_time_request_keyword_alarms', new DateTime());
        dump($request->getSession());
//        dump($this->dealRepository->findNewDealByAlarmUserOrderByDateDesc($this->getUser()));
        return $this->render('pages/profil/keyword_alarms.html.twig', [
            'alarms' => $this->entityManager->getRepository(Deal::class)->findNewDealByAlarmUserOrderByDateDesc($this->getUser())
        ]);
    }


    /**
     * @Route("/settings", name="profil_settings")
     */
    public function settings(): Response
    {
        if ($this->getUser()->isClosed()) return $this->redirectToRoute('home');

        return $this->render('pages/profil/settings.html.twig', []);
    }

    /**
     * @Route({"en": "/my-deals", "fr": "/mes-deals"}, name="profil_published_deals")
     */
    public function publishedDeals(Request $request, PaginatorInterface $paginator): Response
    {
        if ($this->getUser()->isClosed()) return $this->redirectToRoute('home');

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
        if ($this->getUser()->isClosed()) return $this->redirectToRoute('home');

        $userSavedDeals = $paginator->paginate(
            $this->getUser()->getSavedDeals(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/profil/saved_deals.html.twig', [
            "user_saved_deals" => $userSavedDeals,
        ]);
    }

    /**
     * @Route({"en": "/delete-account", "fr": "/supprimer-mon-compte"}, name="profil_delete_account")
     */
    public function softDelete(Request $request, PaginatorInterface $paginator): Response
    {
        if (!$this->getUser() || $this->getUser()->isClosed()) $this->redirectToRoute('home');

        $this->getUser()->setClosed(true);

        $this->entityManager->flush();

        $request->getSession()->invalidate(1);

        $this->addFlash('success', 'Votre compte a été fermé.');

        return $this->redirectToRoute('home');
    }
}
