<?php

namespace App\MessageHandler;

use App\Message\EmailMessage;
 use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
readonly class EmailMessageHandler
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(EmailMessage $message): void
    {
        foreach ($message->getRecipients() as $to) {
            $email = (new Email())
                ->from(new Address($message->getFrom()))
                ->to($to)
                ->subject($message->getSubject())
                ->html($message->getContent());
            $this->mailer->send($email);
        }
    }
}
