<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label_attr' => ['class' => 'bmd-label-floating']])
            ->add('username', TextType::class, ['label_attr' => ['class' => 'bmd-label-floating']])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => array('label' => 'Password', 'required' => true, 'label_attr' => ['class' => 'bmd-label-floating']),
                'second_options' => array('label' => 'Repeat password', 'required' => true, 'label_attr' => ['class' => 'bmd-label-floating']),
                'invalid_message' => 'They are not the same password',
            ])
            ->add('email', EmailType::class, ['label_attr' => ['class' => 'bmd-label-floating']])
            ->add('kingdom', EntityType::class, [
                'mapped' => true,
                'class' => 'App\Entity\Kingdom',
                'placeholder' => 'select kingdom'        
            ] )
        ;
    }



    /*
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, ['label_attr' => ['class' => 'bmd-label-floating']])
            ->add('username', null, ['label_attr' => ['class' => 'bmd-label-floating']])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => array('label' => 'Password', 'required' => true, 'label_attr' => ['class' => 'bmd-label-floating']),
                'second_options' => array('label' => 'Repeat password', 'required' => true, 'label_attr' => ['class' => 'bmd-label-floating']),
                'invalid_message' => 'They are not the same password',
            ])
            ->add('email', EmailType::class, ['label_attr' => ['class' => 'bmd-label-floating']])
            ->add('kingdom', EntityType::class, [
                'mapped' => false,
                'class' => 'App\Entity\Kingdom',
                'placeholder' => 'select',
                'query_builder' => function (EntityRepository $er){
                    return $er->createQueryBuilder('k');
                }
            ] )
        ;
    }*/

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
