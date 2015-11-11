<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 11/11/15 10:59
 */

namespace DspSofts\CronManagerBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Planification extends Constraint
{
    public $message = 'The planification "%string%" is invalid.';
}
