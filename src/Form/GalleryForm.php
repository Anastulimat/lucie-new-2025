<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Gallery;
use App\Entity\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GalleryForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
            ])
            ->add('slug')
            ->add('description')
            ->add('featuredImage', EntityType::class, [
                'class' => Image::class,
                'choice_label' => 'id',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
            ])
            ->add('slugAuto', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'data' => true,
                'label' => 'Générer le slug automatiquement',
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Gallery::class,
        ]);
    }
}
