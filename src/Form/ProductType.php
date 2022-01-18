<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('name')
            ->add('description')
            ->add('brand')
            ->add('price')
            // ->add('createdAt')
            // ->add('updatedAt')
            ->add('category', EntityType::class, [
                'required' => true,
                'class' => Category::class,
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('category')
                        ->where('category.active = 1')
                        ->orderBy('category.name', 'ASC');
                },
                'choice_label' => function ($category) {
                    return $category->getName();
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
