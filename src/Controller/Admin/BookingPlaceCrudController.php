<?php

namespace App\Controller\Admin;

use App\Entity\BookingPlace;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;


class BookingPlaceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BookingPlace::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            
            DateTimeField::new('startDate'),
            DateTimeField::new('endDate'),
            IntegerField::new('location_duration'),
            AssociationField::new('user'),
            AssociationField::new('place'),
            IntegerField::new('total')
        ];
    }
}
