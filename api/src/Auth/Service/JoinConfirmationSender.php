<?php

namespace App\Auth\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use RuntimeException;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

class JoinConfirmationSender
{
    private Swift_Mailer $mailer;
    private Environment $twig;

    public function __construct(Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function send(Email $email, Token $token)
    {
        $message = (new Swift_Message('Join Confirmation'))
            ->setTo($email->getValue())
            ->setBody(
                $this->twig->render('auth/join/confirm.html.twig', ['token' => $token]),
                'text/html'
            );

        if (0 === $this->mailer->send($message)) {
            throw new RuntimeException('Unable to send email.');
        }
    }
}
