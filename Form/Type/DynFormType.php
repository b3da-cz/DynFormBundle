<?php

namespace b3da\DynFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DynFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        if(isset($options['dynamic']) && $options['dynamic'] != null) {
            foreach($options['dynamic'] as $field) {
                $builder->add($field[0], $field[1], $field[2]);
            }
        } else {
            $builder->add('name', TextType::class, ['label' => 'dynForm.field.formName']);
        }
        $builder->add('submit', SubmitType::class, ['label' => 'dynForm.field.submit']);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array('dynamic' => null));
    }
}