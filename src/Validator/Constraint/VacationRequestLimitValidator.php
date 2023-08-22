<?php

namespace App\Validator\Constraints;

use App\Entity\User;
use App\Entity\VacationRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class VacationRequestLimitValidator extends ConstraintValidator
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function validate($value, Constraint $constraint)
    {
        $user = $this->security->getUser();
        // dd($user);
        $vacationRequest = $this->context->getRoot()->getData();

        if (!$user instanceof User) {
            return;
        }

        $startDate = $vacationRequest->getStartDate();
        $endDate = $vacationRequest->getEndDate();

        if (!$startDate || !$endDate) {
            return;
        }

        $daysDifference = $endDate->diff($startDate)->days + 1;
        // dd($daysDifference);

        if ($daysDifference > $user->getDaysLeft()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ limit }}', 20)
                ->addViolation();
        }
    }
}
