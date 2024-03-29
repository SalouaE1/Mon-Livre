<?php

namespace App\Controller\Admin;


use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class OrderCrudController extends AbstractCrudController
{

    private $em;
    private $adminUrlGenerator;

    public function __construct(EntityManagerInterface $em, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->em = $em;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }


    public function configureActions(Actions $actions): Actions
    {
        $updatePreparation = Action::new('updatePreparation', 'Préparation en cours','fas fa-box-open')->linkToCrudAction('updatePreparation');
        
        $updateDelivery = Action::new('updateDelivery', 'Livraison en cours','fas fa-truck')->linkToCrudAction('updateDelivery');
        
        return $actions
        ->add('detail', $updatePreparation)
        ->add('detail', $updateDelivery)
         ->add('index', 'detail');
    }


    // Fonction pour statut de la commande : en préparation
    public function updatePreparation(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(2);
        $this->em->flush();
        $this->addFlash('notice', "<span style='color:green;'><strong>La commande N° ".$order->getReference()." est bien en cours de préparation</strong></span>");
        $url = $this->adminUrlGenerator
        ->setController(OrderCrudController::class)
        ->setAction('index')
        ->generateUrl();

        return $this->redirect($url);
    }


    // Fonction pour statut de la commande : en livraison
    public function updateDelivery(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(3);
        $this->em->flush();
        $this->addFlash('notice', "<span style='color:orange;'><strong>La commande N° ".$order->getReference()." est en cours de livraison</strong></span>");
        $url = $this->adminUrlGenerator
        ->setController(OrderCrudController::class)
        ->setAction('index')
        ->generateUrl();
        return $this->redirect($url);
    }

    //Trier les commandes par Id en ordre décroissant:
    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' =>'Desc']);
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
           IdField::new('id'),
           DateTimeField:: new('createdAT', 'Passée le: '),
           TextField:: new('user.fullname', 'Utilisateur: '),
           TextEditorField:: new('delivery', 'Adresse de livraison: ')->onlyOnDetail(),
           MoneyField:: new('total', 'Total produit')->setCurrency('EUR'),
           TextField:: new('carrierName', 'Transporteur: '),
           MoneyField:: new('carrierPrice', 'Frais de port')->setCurrency('EUR'),
           ChoiceField:: new ('state')->setChoices([
            'Non payée' => 0,
            'Payée' => 1,
            'Préparation en cours' => 2,
            'Livraison en cours' => 3
           ]),

           ArrayField:: new('orderDetails', 'Produits achetés')->hideOnIndex()
        ];
    }
    
}
