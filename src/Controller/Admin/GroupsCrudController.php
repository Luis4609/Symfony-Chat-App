<?php

namespace App\Controller\Admin;

use App\Entity\Groups;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class GroupsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Groups::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
