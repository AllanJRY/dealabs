<?php


namespace App\Service;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Throwable;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Exception;

/**
 * Class Mailer
 * @package App\Service
 */
class Mailer implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private $twig;
    private $mailer;
    private $sender;

    /**
     * Mailer constructor.
     *
     * @param Environment $twig
     * @param MailerInterface $mailer
     * @param string $sender
     */
    public function __construct(Environment $twig, MailerInterface $mailer, string $sender)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->sender = $sender;
    }

    /**
     * @param string $template
     * @param array $vars
     * @param Address|string $to
     * @param null $from
     * @param null $replyTo
     * @return bool
     * @throws TransportExceptionInterface
     * @throws Throwable
     * @throws LoaderError
     * @throws SyntaxError
     */
    public function send(string $template, array $vars, $to, $from = null, $replyTo = null): bool
    {
        if (null === $from) {
            $from = new Address($this->sender, 'Dealabs Mailer');
        }

        $template = $this->twig->resolveTemplate($template);

        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($template->renderBlock('subject', $vars))
            ->html($template->renderBlock('html', $vars))
            ->text($template->renderBlock('text', $vars))
            ->context($vars)
        ;

        if (null !== $replyTo) {
            $email->replyTo(new Address($replyTo));
        }

        try {
            $this->mailer->send($email);
            return true;
        } catch (Exception $e) {
            if (null !== $this->logger) {
                $this->logger->critical('Cannot send mail.', [
                    'exception_message' => $e->getMessage(),
                    'template' => $template,
                    'to' => $to,
                    'from' => $from,
                    'reply_to' => $replyTo,
                    'exception' => $e,
                ]);
            }

            return false;
        }
    }
}
