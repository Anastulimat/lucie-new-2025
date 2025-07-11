<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Gallery;
use App\Entity\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image as ImageConstraint;
use Symfony\Component\Validator\Constraints\All;

class GalleryForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
            ])
            ->add('subtitle', TextType::class, [
                'required' => false,
            ])
            ->add('slug')
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 5,
                ]
            ])
            ->add('visibleInNavigation', CheckboxType::class, [
                'required' => false,
                'label' => 'Afficher dans la navigation principale',
            ])
            ->add('featuredImageFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Image mise en avant',
                'constraints' => [
                    new ImageConstraint([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG ou PNG)',
                    ])
                ],
                'help' => 'Formats acceptés : JPG, PNG. Taille max : 5 Mo'
            ])
            ->add('slugAuto', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'data' => true,
                'label' => 'Générer le slug automatiquement',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
            ])
            ->add('titlePosition', ChoiceType::class, [
                'label' => 'Position du titre sur l\'image',
                'choices' => Gallery::TITLE_POSITIONS,
                'required' => true,
                'expanded' => false,
                'multiple' => false,
                'attr' => [
                    'class' => 'form-select'
                ],
                'help' => 'Choisissez où positionner le titre sur l\'image à la une'
            ])
            ->add('imagesFiles', FileType::class, [
                'required' => false,
                'multiple' => true,
                'label' => 'Images de la galerie',
                'constraints' => [
                    new All([
                        'constraints' => [
                            new ImageConstraint([
                                'maxSize' => '5M',
                                'mimeTypes' => [
                                    'image/jpeg',
                                    'image/png',
                                ],
                                'mimeTypesMessage' => 'Veuillez uploader des images valides (JPG ou PNG)',
                            ])
                        ]
                    ])
                ],
                'help' => 'Vous pouvez sélectionner plusieurs images. Formats acceptés : JPG, PNG. Taille max : 5 Mo par image'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Gallery::class,
        ]);
    }
}
