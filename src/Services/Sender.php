<?php

namespace App\Services;

use PharIo\Manifest\Email;
use Symfony\Component\Mailer\MailerInterface;

class Sender
{

    public function __construct(private MailerInterface $mailer) {}

    public function sendEmail(string $subject, string $text, string $dest)
    {
        $email = new Email();
        $email->subject($subject)
            ->text($text)
            ->from('no-reply@havingFun.com')
            ->to($dest);

        $this->mailer->send($email);

    }

}