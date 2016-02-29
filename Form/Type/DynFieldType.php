<?php

namespace b3da\DynFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DynFieldType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('type', ChoiceType::class, [
            'label' => 'dynForm.field.type',
            'choices_as_values' => true,
            'choices' => [
                'TextType' => TextType::class,
                'TextareaType' => TextareaType::class,
                'NumberType' => NumberType::class,
                'ChoiceType' => ChoiceType::class,
                'CheckboxType' => CheckboxType::class
            ]
        ]);
        $builder->add('label', TextType::class, ['label' => 'dynForm.field.title']);
        $builder->add('required', CheckboxType::class, ['label' => 'dynForm.field.required', 'required' => false]);
        $builder->add('data', HiddenType::class, ['label' => 'dynForm.field.data', 'required' => false]);
        $builder->add('submit', SubmitType::class, ['label' => 'dynForm.field.submit']);
    }
}