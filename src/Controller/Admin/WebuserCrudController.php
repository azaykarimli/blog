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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;



class WebuserCrudController extends AbstractCrudController
{

    public $userPasswordHasherInterface;
    public function __construct(UserPasswordHasherInterface $userPasswordHasherInterface, EntityManagerInterface $entityManager)
    {
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;
    }



    public static function getEntityFqcn(): string
    {
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

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->encodePassword($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->encodePassword($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    private function encodePassword(Webuser $user)
    {
        if ($user->getPassword() !== null) {
            $user->setPassword(
                $this->userPasswordHasherInterface->hashPassword(
                    $user,
                    $user->getPassword()
                )
            );
        }
    }


}
