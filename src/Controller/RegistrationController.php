<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Security\UserAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator): Response
    {
        if ($this->getUser()) {
//            throw new NotFoundHttpException();
            return $this->redirectToRoute('home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
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
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user);
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('pages/authentication/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // TODO Translation
        $this->addFlash('success', 'Your email address has been verified.');

        $referer = filter_var($request->headers->get('referer'), FILTER_SANITIZE_URL);
        // TODO: redirect to profil or elsewhere once implmentation of those are made.
        return $this->redirectToRoute('home');
    }

    /**
     * @Route({"en": "send/verify-email/{id}", "fr": "envoi/confirmation-email/{id}"}, name="send_verify_email")
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function sendVerificationEmail(Request $request, User $user): Response
    {
        $hasBeenSent = false;
        if ($this->getUser()->getId() !== $user->getId()) {
            throw new NotFoundHttpException();
        }

        if ($request->headers->get('referer') === $request->getUri()) {
            return $this->redirectToRoute('home');
        }

        if (!$user->isVerified()) {
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user);
            $hasBeenSent = true;
        }

        return $this->render('pages/authentication/confirmation_email_sent.html.twig', [
            'email_sent' => $hasBeenSent,
        ]);
    }

}
