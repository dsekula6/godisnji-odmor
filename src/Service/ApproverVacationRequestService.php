<?php

namespace App\Service;

use App\Repository\VacationRequestRepository;
use App\Repository\ProjectRepository;
use App\Entity\User;
use App\Entity\VacationRequest;
use Doctrine\ORM\EntityManagerInterface;

class ApproverVacationRequestService
{
    public function __construct(
        private VacationRequestRepository $vacationRequestRepository,
        private ProjectRepository $projectRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function getFilteredVacationRequests(User $user): array
    {
        $vacationRequests = $this->vacationRequestRepository->findAll();
        $filteredVacationRequests = [];
        $currentUserTeamIds = [];

        foreach ($this->projectRepository->findBy(['active' => true]) as $project) {
            if ($project->getProjectLead()->getId() == $user->getId()) {
                foreach ($project->getProjectTeams() as $team) {
                    $currentUserTeamIds[] = $team->getId();
                }
            }
        }
        foreach ($user->getTeamMembers() as $teamMember) {
            if ($teamMember->getTeamRole() == 'team_lead') {
                $currentUserTeamIds[] = $teamMember->getTeam()->getId();
            }
        }
        $currentUserTeamIds = array_unique($currentUserTeamIds);

        $filteredVacationRequests = array_filter($vacationRequests, function ($request) use ($currentUserTeamIds) {
            $user = $request->getUser();
            foreach ($user->getTeamMembers() as $teamMember) {
                if (in_array($teamMember->getTeam()->getId(), $currentUserTeamIds)) {
                    return true;
                }
            }
            return false;
        });

        return $filteredVacationRequests;
    }

    public function approveVacationRequest(VacationRequest $vacationRequest, User $user): void
    {
        $teamLeadId = 0;
        $projectTeams = [];
        foreach ($user->getTeamMembers() as $teamMember) {
            if ($teamMember->getTeamRole() == 'team_lead') {
                $teamLeadId = $teamMember->getTeam()->getId();
            }
        }
        foreach ($this->projectRepository->findBy(['active' => true]) as $project) {
            if ($project->getProjectLead()->getId() == $user->getId()) {
                foreach ($project->getProjectTeams() as $team) {
                    $projectTeams[] = $team->getId();
                }
            }
        }
        $projectTeams = array_unique($projectTeams);

        foreach ($vacationRequest->getUser()->getTeamMembers() as $userTeamMember) {
            // ako je approver team lead trenutnog usera
            if ($userTeamMember->getTeam()->getId() == $teamLeadId) {
                $vacationRequest->setTeamLeadApproved(true);
            }

            //ako je approver project lead trenutnog usera
            if (in_array($userTeamMember->getTeam()->getId(), $projectTeams)) {
                $vacationRequest->setProjectLeadApproved(true);
            }
        }
        // dd($vacationRequest);
        $this->entityManager->flush();
    }
}
