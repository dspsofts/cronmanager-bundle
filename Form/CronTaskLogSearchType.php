<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 10/11/15 08:35
 */

namespace DspSofts\CronManagerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CronTaskLogSearchType extends AbstractType
{
    public function getName()
    {
        return 'dspsofts_cm_crontask_log_search';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DspSofts\CronManagerBundle\Entity\Search\CronTaskLogSearch',
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateStart', 'dspsofts_date_picker')
            ->add('search', 'submit');
    }
}
