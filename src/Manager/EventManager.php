<?php

namespace App\Manager;

use App\Repository\CommentsRepository;
use App\Repository\EventRepository;
use App\Repository\UserRepository;

class EventManager
{
    private $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    //All event
    public function findAll(){
        return $this->eventRepository->findAll();
    }

    //Give event
    public function getEventByTitle($title, $now, $page) {
        return $this->eventRepository->getEventByTitle($title, $now, $page);
    }

    //Give number page for paging
    public function getNumberPage($nbEvents)
    {
        return round($nbEvents);
    }

}