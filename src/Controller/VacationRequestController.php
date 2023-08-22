<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\VacationRequest;
use App\Form\VacationRequestType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\VacationRequestRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\ApproverVacationRequestService;
use App\Service\VacationRequestService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/vacation-request')]
class VacationRequestController extends AbstractController
{
    #[Route('/', name: 'app_vacation_request_index', methods: ['GET'])]
    public function index(VacationRequestRepository $vacationRequestRepository, ApproverVacationRequestService $approverVacationRequestService): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_vacation_request_index', [], Response::HTTP_SEE_OTHER);
        }
        $roles = $user->getRoles();

        if (in_array('ROLE_ADMIN', $roles)) {
            return $this->render('vacation_request/index.html.twig', [
                'vacation_requests' => $vacationRequestRepository->findAll(),
                'user_roles' => $user->getRoles(),
            ]);
        }

        if (in_array('ROLE_TEAM_LEAD', $roles) || in_array('ROLE_PROJECT_LEAD', $roles)) {
            return $this->render('vacation_request/index.html.twig', [
                'vacation_requests' => $approverVacationRequestService->getFilteredVacationRequests($user),
                'user_name' => $user->getFirstName() . ' ' . $user->getLastName(),
                'user_roles' => $user->getRoles(),
            ]);
        }
        return $this->render('vacation_request/index.html.twig', [
            'vacation_requests' => $vacationRequestRepository->findBy(['user' => ['id' => $user->getId()]]),
            'days_left' => $user->getDaysLeft(),
            'user_roles' => $user->getRoles(),
        ]);
    }

    #[Route('/new', name: 'app_vacation_request_new', methods: ['GET', 'POST'])]
    public function new(Request $request, VacationRequestService $vacationRequestService): Response
    {
        $vacationRequest = new VacationRequest();
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_vacation_request_index', [], Response::HTTP_SEE_OTHER);
        }
        $form = $this->createForm(VacationRequestType::class, $vacationRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $vacationRequestService->createVacationRequest($vacationRequest, $user);
            return $this->redirectToRoute('app_vacation_request_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vacation_request/new.html.twig', [
            'vacation_request' => $vacationRequest,
            'form' => $form,
            'days_left' => $user->getDaysLeft(),
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
        if ($this->isCsrfTokenValid('delete' . $vacationRequest->getId(), $request->request->get('_token'))) {
            $entityManager->remove($vacationRequest);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vacation_request_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/approve', name: 'app_vacation_request_approve', methods: ['GET'])]
    public function approve(VacationRequest $vacationRequest, ApproverVacationRequestService $approverVacationRequestService): Response
    {
        $approverVacationRequestService->approveVacationRequest($vacationRequest, $this->getUser());

        return $this->redirectToRoute('app_vacation_request_index', [], Response::HTTP_SEE_OTHER);
    }
}
