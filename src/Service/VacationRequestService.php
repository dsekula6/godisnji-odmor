<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\VacationRequest;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\VacationRequestRepository;

class VacationRequestService
{
    public function __construct(
        private VacationRequestRepository $vacationRequestRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function createVacationRequest(VacationRequest $vacationRequest, User $user): void
    {
        $vacationRequest->setStatus('pending');
        $vacationRequest->setTeamLeadApproved(false);
        $vacationRequest->setProjectLeadApproved(false);
        $vacationRequest->setUser($user);
        $startDate = $vacationRequest->getStartDate();
        $endDate = $vacationRequest->getEndDate();
        $user->setDaysLeft(20 - $endDate->diff($startDate)->days - 1);

        $this->entityManager->persist($vacationRequest);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
