<?php

/**
 * CronTask type.
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 01/11/15 14:23
 */

namespace DspSofts\CronManagerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CronTaskType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DspSofts\CronManagerBundle\Entity\CronTask',
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Nom',
            ))
            ->add('planification', TextType::class, array(
                'label' => 'Planification',
            ))
            ->add('type', ChoiceType::class, array(
                'choices_as_values' => true,
                'choices' => array(
                    'Commande symfony' => 'SYMFONY',
                    'Ligne de commande' => 'COMMAND',
                    'URL' => 'URL',
                )
            ))
            ->add('command')
            ->add('timeout')
            ->add('isActive', CheckboxType::class, array(
                'required' => false,
            ))
            ->add('isUnique', CheckboxType::class, array(
                'required' => false,
            ))
            ->add('save', SubmitType::class);
    }
}
