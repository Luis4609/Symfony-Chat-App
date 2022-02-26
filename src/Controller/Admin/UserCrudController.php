<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('New User')
            ->setEntityLabelInPlural('Users')
            ->setSearchFields(['email'])
            ->setDefaultSort(['id' => 'ASC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('user'));
    }

    // public function configureFields(string $pageName): iterable
    // {
    //     yield AssociationField::new('id');
    //     yield TextField::new('firstName');
    //     yield EmailField::new('email');
    //     yield TextareaField::new('lastName')
    //         ->hideOnIndex();
    //     yield TextField::new('avatar')
    //         ->onlyOnIndex();

    //     $createdAt = DateTimeField::new('createdAt')->setFormTypeOptions([
    //         'html5' => true,
    //         //    'years' => range(date('Y'), date('Y') 5),
    //         'widget' => 'single_text',
    //     ]);
    //     if (Crud::PAGE_EDIT === $pageName) {
    //         yield $createdAt->setFormTypeOption('disabled', true);
    //     } else {
    //         yield $createdAt;
    //     }
    // }
}
