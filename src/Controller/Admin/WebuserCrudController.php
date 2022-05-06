<?php

namespace App\Controller\Admin;

use App\Entity\Webuser;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class WebuserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        dd($_POST["Webuser"]["password"]);
        dd($_POST["password"]);
        $user = Webuser();

        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );
        return Webuser::class;
    }

    


    public function configureFields(string $pageName): iterable
    {
        //dd(new('id'));
        yield IdField::new('id')->hideOnForm();
        yield EmailField::new('email');
        yield TextField::new('password')->setFormType(PasswordType::class)->hideOnIndex();
        $roles = ['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_MODERATOR', 'ROLE_USER'];
        yield ChoiceField::new('roles')
            ->setChoices(array_combine($roles, $roles))
            ->allowMultipleChoices()
            ->renderExpanded();
        /*
        return [
            IdField::new('id'),
            EmailField::new('email'),
            //TextField::new('roles'),
            // TextField::new('title'),
            // TextEditorField::new('description'),
        
        ];
        */
    }
}
