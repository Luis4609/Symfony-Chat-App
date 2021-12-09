<?php

namespace App\Form;

use App\Entity\Messages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('FromUserId')
            ->add('ToUserId')
            ->add('Text')
            ->add('Timestamp')
            ->add('IsRead')
            ->add('AttachFile')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Messages::class,
        ]);
    }
}
