<?php

/**
 * Date picker.
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 07/11/15 23:31
 */

namespace DspSofts\CronManagerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatePickerType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'html5' => false,
            'format' => 'dd/MM/yyyy',
            'widget' => 'single_text',
            'attr' => array(
                'class' => 'datepicker',
            ),
        ));
    }

    public function getParent()
    {
        return 'date';
    }
}
