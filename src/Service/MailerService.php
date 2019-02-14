<?php
/**
 * Created by PhpStorm.
 * User: matthieuparis
 * Date: 12/02/2019
 * Time: 16:50
 */

namespace App\Service;


use App\Entity\Event;
use App\Entity\User;

class MailerService
{
    private $mailer;
    private $templates;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $templates)
    {
        $this->mailer = $mailer;
        $this->templates = $templates;
    }

    public function sendMail(User $user, Event $event)
    {
        $message = (new \Swift_Message("Une conférence vient d'être créer !"))
            ->setFrom('admin@projet.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->templates->render(
                // templates/emails/registration.html.twig
                    'emails/index.html.twig',
                    ['user' => $user,
                        'event' => $event,
                        'title' => "Une conférence vient d'être créer !"]
                ),
                'text/html'
            )/*
             * If you also want to include a plaintext version of the message
            ->addPart(
                $this->renderView(
                    'emails/registration.txt.twig',
                    ['name' => $name]
                ),
                'text/plain'
            )
            */
        ;

        $this->mailer->send($message);
    }

}