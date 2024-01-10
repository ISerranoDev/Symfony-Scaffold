<?php

namespace App\Utils\Classes;

use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class MailerService
{
    private MailerInterface $mailer;
    private ParameterBagInterface $parameterBag;

    public function __construct(MailerInterface $mailer, ParameterBagInterface $parameterBag)
    {
        $this->mailer = $mailer;
        $this->parameterBag = $parameterBag;
    }

    public function sendEmail(string $to, string $subject, string $template, array $context = [], array $attachments = []): bool
    {
        $email = (new TemplatedEmail())->from($this->parameterBag->get('mailer-email'))
            ->to($to)
            ->subject('Soporte - ' . $subject)
            ->htmlTemplate($template)
            ->context($context);

        foreach ($attachments as $name => $attachment){
            $email->attach($attachment, $name);
        }

        try {
            $this->mailer->send($email);

            return true;
        }catch (TransportExceptionInterface $exception){
            return false;
        }

    }
}