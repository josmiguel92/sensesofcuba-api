<?php

namespace App\Form;

use App\Entity\TranslatedDocument;
use App\Entity\UsersGroup;
use App\Repository\UsersGroupRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class TranslatedDocumentType extends AbstractType
{
    private array $groupsNames;

    /**
     * TranslatedDocumentType constructor.
     */
    public function __construct(UsersGroupRepository $groupRepository)
    {
        $this->groupsNames = $groupRepository->getUserGroupNamesArray();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $groupsInputOptions = [
            'choice_label' => function ($name) {
                return (string) $name;
            },
            'choices' => $this->groupsNames,
            'multiple' => true,
            'expanded' => true,
            'required' => false
        ];

        $builder
            ->add('title', TextType::class, ['label' => 'Internal reference title', 'required' => false])
            ->add('enFile', SocFileType::class, ['label' => 'English Version file', 'required' => false])
//            ->add('esFile', SocFileType::class, ['label'=>'Spanish Version file', 'required'=>false])
            ->add('deFile', SocFileType::class, ['label' => 'German Version file', 'required' => false])

            ->add('showForGroups', ChoiceType::class,
                array_merge($groupsInputOptions, [
                    'help' => 'If no group is selected, this document will be show to all users except if the user is in one of the groups selected for hide the document.'
                ])
            )
            ->add('hideForGroups', ChoiceType::class, array_merge($groupsInputOptions, [
                'help' => 'Only select the group that shouldn\'t see this document.'
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TranslatedDocument::class,
            'attr' => ['class' => 'translated-doc'],
            'help' => 'Clear all the fields and delete the files linked (if any) to remove this entry of document'
        ]);
    }
}
