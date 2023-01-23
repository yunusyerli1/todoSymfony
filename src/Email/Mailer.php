<?php

namespace App\Email;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Mailer as Mailing;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use App\Entity\User;

class Mailer
{
    private $twig;

    public function __construct(
        Environment $twig,
        //public MailerInterface $mailer
        )
    {
        $this->twig = $twig;
    }

    public function sendConfirmationEmail(User $user, )
    {
        $transport = Transport::fromDsn('smtp://yunusyerli1@gmail.com:vwxkmjxxvdgjbqhc@smtp.gmail.com/587');
        $mailer = new \Symfony\Component\Mailer\Mailer($transport);
        $body = $this->twig->render('email\confirmation.html.twig', ['user' => $user]);

        $email = (new Email())
            ->from('yunusyerli1@gmail.com')
            ->to($user->getEmail())
            ->subject('Please confirm your account!')
            //->text()
            ->html($body);

        $mailer->send($email);
    }
}
