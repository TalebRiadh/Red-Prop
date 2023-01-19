<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,array(
        'attr' => array(
                'placeholder' => 'Nom De Produit',
        ),
))
            ->add('category', EntityType::class, [
            'class' => Category::class,
            'choice_label' => 'name'])
            
            ->add('measurement',ChoiceType::class, [
                'choices'  => [
                    'Kg' => 'Kg',
                    'Littre' => 'Litre',
                ],
                    ])
            ->add('state',CheckboxType::class,[
                'required' => false, 
                ]
            )
            ->add('imageFile',FileType::class,[
                'required' => false
            ])
            ->add('quantity')
            ->add('price',MoneyType::class,array(
                'currency'=>'USD / EURO',
                'attr' => [
                'type' => 'number',
                'placeholder' => 'x.xx',
            ],

     
))
            ->add('Discription',TextareaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
