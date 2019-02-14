<?php

namespace App\Manager;

use App\Repository\CommentsRepository;
use App\Repository\EventRepository;
use App\Repository\UserRepository;

class EventsManager
{
    private $eventsRepository;

    public function __construct(EventRepository $eventsRepository)
    {
        $this->eventsRepository = $eventsRepository;
    }

    //All event
    public function findAll(){
        return $this->eventsRepository->findAll();
    }

    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return $this->eventsRepository->find($id, $lockMode, $lockVersion);
    }

    //Give event
    public function getEventByTitle($title, $now, $page) {
        return $this->eventsRepository->getEventByTitle($title, $now, $page);
    }

    //Get All User Events, 6 events by page
    public function getEventUserByTitle($title, $now, $page, $ids){
        return $this->eventsRepository->getEventUserByTitle($title, $now, $page, $ids);
    }

    //Give number page for paging
    public function getNumberPage($nbEvents)
    {
        return round($nbEvents);
    }

}