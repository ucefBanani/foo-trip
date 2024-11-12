<?php

namespace App\Message;

readonly class EmailMessage
{
    public function __construct(
        private string $from,
        private array  $recipients,
        private string $subject,
        private string $content,
        private array  $context = [],
    ) {
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getRecipients(): array
    {
        return $this->recipients;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
