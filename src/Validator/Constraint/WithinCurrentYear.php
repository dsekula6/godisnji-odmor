<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class WithinCurrentYear extends Constraint
{
    public $message = 'The selected dates must be within the current year.';
}
