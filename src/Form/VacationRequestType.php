<?php

namespace App\Form;

use App\Entity\VacationRequest;
use Symfony\Component\Form\AbstractType;
use App\Validator\Constraints\WithinCurrentYear;
use Symfony\Component\Form\FormBuilderInterface;
use App\Validator\Constraints\VacationRequestLimit;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VacationRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start_date', null, [
                'constraints' => [
                    new WithinCurrentYear(),
                ],
            ])
            ->add('end_date', null, [
                'constraints' => [
                    new VacationRequestLimit(),
                    new WithinCurrentYear(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VacationRequest::class,
        ]);
    }
}
