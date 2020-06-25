<?php
declare(strict_types=1);

namespace DevPro\Application;

use Common\EventDispatcher\EventDispatcher;
use DevPro\Domain\Model\Training\Training;
use DevPro\Domain\Model\Training\TrainingId;
use DevPro\Domain\Model\Training\TrainingRepository;
use DevPro\Domain\Model\User\UserId;

final class CreateTraining
{
    /** @var TrainingRepository */
    private $repository;
    /** @var EventDispatcher */
    private $eventDispatcher;

    public function __construct(TrainingRepository $repository, EventDispatcher $eventDispatcher)
    {
        $this->repository = $repository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function create(string $organizer, string $title, string $date): TrainingId
    {
        $training = Training::schedule(
            $this->repository->nextIdentity(),
            UserId::fromString($organizer),
            $title,
            new \DateTimeImmutable($date)
        );
        $this->repository->save(
            $training
        );

        $this->eventDispatcher->dispatchAll($training->releaseEvents());

        return $training->trainingId();
    }
}
