<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\VacationRequest;
use App\Form\VacationRequestType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\VacationRequestRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/vacationrequest')]
class VacationRequestController extends AbstractController
{
    #[Route('/', name: 'app_vacation_request_index', methods: ['GET'])]
    public function index(VacationRequestRepository $vacationRequestRepository): Response
    {
        $user = $this->getUser();

        foreach ($user->getAppRoles() as $value) {
            if ($value->getRoleName()=='admin') {
                //admin view
                return $this->render('vacation_request/admin_index.html.twig', [
                    'vacation_requests' => $vacationRequestRepository->findAll(),
                ]);
            }
            else if ($value->getRoleName()=='approver') {
                //approver view
                return $this->render('vacation_request/approver_index.html.twig', [
                    'vacation_requests' => $vacationRequestRepository->findAll(),
                ]);
            }
        }
        // Employee view
        return $this->render('vacation_request/employee_index.html.twig', [
            'vacation_requests' => $vacationRequestRepository->findAll(),
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
}
