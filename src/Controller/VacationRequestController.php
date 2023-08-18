<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Entity\VacationRequest;
use App\Form\VacationRequestType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\VacationRequestRepository;
use PhpParser\Node\Expr\Cast\Array_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/vacationrequest')]
class VacationRequestController extends AbstractController
{
    #[Route('/', name: 'app_vacation_request_index', methods: ['GET'])]
    public function index(VacationRequestRepository $vacationRequestRepository, ProjectRepository $projectRepository): Response
    {
        $user = $this->getUser();

        foreach ($user->getAppRoles() as $value) {
            if ($value->getRoleName()=='admin') {
                // admin view
                return $this->render('vacation_request/admin_index.html.twig', [
                    'vacation_requests' => $vacationRequestRepository->findAll(),
                ]);
            }
            else if ($value->getRoleName() == 'team_lead' || $value->getRoleName() == 'project_lead') {
                // approver view
                $vacationRequests = $vacationRequestRepository->findAll();
                $filteredVacationRequests = [];
                $currentUserTeamIds = [];

                // dodaj sve timove od projekta od kojeg je approver user project lead
                foreach ($projectRepository->findAll() as $project) {
                    if ($project->getProjectLead()->getId() == $user->getId()) {
                        foreach ($project->getProjectTeams() as $team) {
                            $currentUserTeamIds[] = $team->getId();
                        }
                    }
                }

                // dodaj tim ako je user team lead nekog teama
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
            
                
                return $this->render('vacation_request/approver_index.html.twig', [
                    'vacation_requests' => $filteredVacationRequests,
                    'user_name' => $user->getFirstName().' '.$user->getLastName(),
                    'user_roles' => $user->getAppRoles(),
                ]);
            }
        }
        // Employee view
        return $this->render('vacation_request/employee_index.html.twig', [
            'vacation_requests' => $vacationRequestRepository->findBy(['user' => [ 'id' => $user->getId()]]),
        ]);
    }
    

    #[Route('/new', name: 'app_vacation_request_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vacationRequest = new VacationRequest();
        $form = $this->createForm(VacationRequestType::class, $vacationRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $vacationRequest->setStatus('pending');
            $vacationRequest->setTeamLeadApproved(false);
            $vacationRequest->setProjectLeadApproved(false);
            $vacationRequest->setUser($this->getUser());
            $entityManager->persist($vacationRequest);
            $entityManager->flush();

            return $this->redirectToRoute('app_vacation_request_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vacation_request/new.html.twig', [
            'vacation_request' => $vacationRequest,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vacation_request_show', methods: ['GET'])]
    public function show(VacationRequest $vacationRequest): Response
    {
        return $this->render('vacation_request/show.html.twig', [
            'vacation_request' => $vacationRequest,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vacation_request_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, VacationRequest $vacationRequest, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VacationRequestType::class, $vacationRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_vacation_request_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vacation_request/edit.html.twig', [
            'vacation_request' => $vacationRequest,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vacation_request_delete', methods: ['POST'])]
    public function delete(Request $request, VacationRequest $vacationRequest, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vacationRequest->getId(), $request->request->get('_token'))) {
            $entityManager->remove($vacationRequest);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vacation_request_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/approve', name: 'app_vacation_request_approve', methods: ['GET'])]
    public function approve(VacationRequest $vacationRequest, EntityManagerInterface $entityManager, ProjectRepository $projectRepository): Response
    {
        $user = $this->getUser();
        $teamLeadId = 0;
        $projectTeams = [];
        foreach ($user->getTeamMembers() as $teamMember) {
            if ($teamMember->getTeamRole() == 'team_lead') {
                $teamLeadId = $teamMember->getTeam()->getId();
            }
            
        }
        foreach ($projectRepository->findAll() as $project) {
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
        $entityManager->flush();
        
        return $this->redirectToRoute('app_vacation_request_index', [], Response::HTTP_SEE_OTHER);
    }

}
