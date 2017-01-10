<?php

/**
 * Date and time picker.
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 07/11/15 23:37
 */

namespace DspSofts\CronManagerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimePickerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dateOptions = $builder->get('date')->getOptions();

        $options = array(
            'html5' => false,
            'format' => 'dd/MM/yyyy',
            'attr' => array(
                'class' => 'datepicker',
            ),
        );

        $dateOptions = array_merge($dateOptions, $options);

        $builder
            ->remove('date')
            ->add('date', DatePickerType::class, $dateOptions)
        ;
    }

    public function getParent()
    {
        return 'datetime';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'date_widget' => 'single_text',
            'time_widget' => 'choice',
        ));
    }
}
