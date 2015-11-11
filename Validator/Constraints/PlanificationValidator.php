<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 11/11/15 11:03
 */

namespace DspSofts\CronManagerBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PlanificationValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $numbers= array(
            '[0-5]?\d',
            '[01]?\d|2[0-3]',
            '0?[1-9]|[12]\d|3[01]',
            '[1-9]|1[012]',
            '[0-7]',
        );

        $valid = true;
        $entries = explode(' ', $value);
        if (count($entries) != 5) {
            $valid = false;
        }

        foreach ($entries as $i => $entry) {
            if (strstr($entry, ',')) {
                $subEntries = explode(',', $entry);
            } else {
                $subEntries = array($entry);
            }

            foreach ($subEntries as $subEntry) {
                if (strstr($subEntry, '/')) {
                    $test = explode('/', $subEntry);
                    if (count($test) != 2) {
                        $valid = false;
                        break;
                    }

                    if ($test[0] != '*' && !preg_match('/' . $numbers[$i] . '/', $test[0])) {
                        $valid = false;
                        break;
                    }

                    if (!preg_match('/' . $numbers[$i] . '/', $test[1])) {
                        $valid = false;
                        break;
                    }
                } else {
                    if ($subEntry != '*' && !preg_match('/' . $numbers[$i] . '/', $subEntry)) {
                        $valid = false;
                        break;
                    }
                }
            }
            if ($valid === false) {
                break;
            }
        }

        if (!$valid) {
            $this->context->addViolation(
                $constraint->message,
                array('%string%' => $value)
            );
        }
    }
}
