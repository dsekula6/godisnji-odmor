<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class VacationRequestLimit extends Constraint
{
    public $message = 'The total vacation days requested for the year cannot exceed {{ limit }} days.';
}
