<?php

namespace App\Model;

final class ContactMessage
{
    public string $name;
    public string $email;
    public ?string $subject = null;
    public ?string $message = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}