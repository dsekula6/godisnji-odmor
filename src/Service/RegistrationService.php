<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\TeamMember;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function registerUser(User $user, Form $form): void
    {
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );

        $team = $form->get('team')->getData();
        $teamMember = new TeamMember();
        $teamMember->setUser($user);
        $teamMember->setTeam($team);
        $teamMember->setTeamRole('member');
        $app_roles = $form->get('app_roles')->getData();

        foreach ($app_roles as $role) {
            if ($role->getRoleName() == 'team_lead') {
                $teamMember->setTeamRole('team_lead');
            }
        }
        $user->addTeamMember($teamMember);
        $user->setDaysLeft(20);

        // symfony roles
        $user->setRoles(explode(',', $form->get('roles')->getData()));

        $this->entityManager->persist($user);
        $this->entityManager->persist($teamMember);
        $this->entityManager->flush();
    }
}
