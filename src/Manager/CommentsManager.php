<?php

namespace App\Manager;

use App\Entity\Event;
use App\Entity\User;
use App\Repository\CommentsRepository;

class CommentsManager
{
    private $commentsRepository;

    public function __construct(CommentsRepository $commentsRepository)
    {
        $this->commentsRepository = $commentsRepository;
    }

    //All event
    public function findAll()
    {
        return $this->commentsRepository->findAll();
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->commentsRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->commentsRepository->findOneBy($criteria, $orderBy);
    }

    //Get top 10 events
    public function getTopTen()
    {
        return $this->commentsRepository->getTopTen();
    }

    //Get Avarage for one event
    public function getAverage(Event $event)
    {
        return $this->commentsRepository->getMoyenne($event);
    }

    //Get all User Id Rated Events
    public function getUserEventRating($user)
    {
        return $this->commentsRepository->getUserEventRating($user);
    }

    //User 3 Last Rated Event
    public function userLastRateEvent(User $user)
    {
        return $this->commentsRepository->userLastRate($user);
    }
}