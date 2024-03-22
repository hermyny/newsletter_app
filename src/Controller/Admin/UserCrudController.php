<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\CategoryRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {


        return [
            // IdField::new('id'),
            CollectionField::new('categories')
          
            ->setLabel('User Categories'),
            TextField::new('email'),
            DateField::new('created_at'),
            TextField::new('validation_token'),
            BooleanField::new('is_rgpd'),
            BooleanField::new('is_valid'),
        ];
    }
    
}
