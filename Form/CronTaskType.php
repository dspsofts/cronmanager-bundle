<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 01/11/15 14:23
 */

namespace DspSofts\CronManagerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CronTaskType extends AbstractType
{
    public function getName()
    {
        return 'dspsofts_cm_crontask';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DspSofts\CronManagerBundle\Entity\CronTask',
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'Nom',
            ))
            ->add('planification', 'text', array(
                'label' => 'Planification',
            ))
            ->add('type', 'choice', array(
                'choices' => array(
                    'SYMFONY' => 'Commande symfony',
                    'COMMAND' => 'Ligne de commande',
                    'URL' => 'URL',
                )
            ))
            ->add('command')
            ->add('save', 'submit');
    }
}
