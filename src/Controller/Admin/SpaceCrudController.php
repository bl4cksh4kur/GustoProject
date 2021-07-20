<?php

namespace App\Controller\Admin;

use App\Entity\Space;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SpaceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Space::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            TextField::new('type'),
            TextField::new('nbplace'),
            IntegerField::new('price'),
            TextEditorField::new('description'),
            TextField::new('adress'),

            ImageField::new('illustration')
            ->setBasePath('uploads')
            ->setUploadDir('/public/uploads') 
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(false),


        ];
    }
    
}
