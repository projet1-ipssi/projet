<?php

namespace App\Manager;

use App\Repository\CommentsRepository;
use App\Repository\EventRepository;
use App\Repository\UserRepository;

class CommentsManager
{
    private $userRepository;
    private $eventRepository;
    private $commentsRepository;

    public function __construct(UserRepository $userRepository, EventRepository $eventRepository, CommentsRepository $commentsRepository)
    {
        $this->userRepository = $userRepository;
        $this->eventRepository = $eventRepository;
        $this->commentsRepository = $commentsRepository;
    }

    //All event
    public function findAll(){
        return $this->commentsRepository->findAll();
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) {
        return $this->commentsRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria, array $orderBy = null) {
        return $this->commentsRepository->findOneBy($criteria, $orderBy);
    }

    //User 3 Last Rated Event
    public function userLastRateEvent() {
        return $this->commentsRepository->userLastRate();
    }
}