<?php

namespace App\Controller;

use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $now = new \DateTime();
        $eventRepository = $this->getDoctrine()->getRepository(Event::class);
        $events = $eventRepository->getEventByDate($now);
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'events'=> $events
        ]);
    }
}
