<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class WithinCurrentYearValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$value) {
            return;
        }

        $currentYear = (int)date('Y');
        $selectedYear = (int)$value->format('Y');

        if ($selectedYear !== $currentYear) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
