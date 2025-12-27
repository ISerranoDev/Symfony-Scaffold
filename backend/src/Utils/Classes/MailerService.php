<?php

namespace App\Utils\Classes;

use App\Kernel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

readonly class MailerService
{

    public function __construct(
        private MailerInterface        $mailer,
        private ParameterBagInterface  $parameterBag,
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function sendEmail(string|array $to, string $subject, string $template, array $context = [], array $attachments = []): bool
    {
        if($this->parameterBag->get('app-env') == Kernel::DEV_ENV){
            return true;
        }

        $from = new Address($this->parameterBag->get('mailer-email'), $this->parameterBag->get("app-name"));

        foreach ($context as $key => &$value){
            if(is_object($value) && str_contains(get_class($value), 'App\\Entity\\')){
                $this->entityManager->refresh($value);
            }
        }

        $email = (new TemplatedEmail())->from($from)
            ->sender($from)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);

        if (is_array($to)) {
            $email->to(...$to);
        } else {
            $email->to($to);
        }

        foreach ($attachments as $name => $attachment){
            $email->attach($attachment, $name);
        }

        // Romper el threading de Gmail
        $headers = $email->getHeaders();
        $headers->addTextHeader('X-Entity-Ref-ID', uniqid('', true));

        try {
            $this->mailer->send($email);

            return true;
        }catch (TransportExceptionInterface $exception){
            return false;
        }

    }
}
