<?php

namespace App\Controller;

use App\Entity\Badge;
use App\Entity\Deal;
use App\Entity\File;
use App\Form\UserType;
use App\Security\EmailVerifier;
use App\Security\UserAuthenticator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

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
    /**
     * @var EmailVerifier
     */
    private $emailVerifier;

    public function __construct(EntityManagerInterface $entityManager, EmailVerifier $emailVerifier)
    {
        $this->entityManager = $entityManager;
        $this->emailVerifier = $emailVerifier;

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
    public function settings(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator): Response
    {
        if ($this->getUser()->isClosed()) return $this->redirectToRoute('home');

        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();

            $pictureFile = $form->get('avatar')->getData();
            if ($pictureFile) {
                $file = new File();
                $file->setFile($pictureFile);
                $file->preUpload();
                $entityManager->persist($file);
                $entityManager->flush();
                $file->upload();
                $user->setAvatar($file);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user);

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('pages/profil/settings.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
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
