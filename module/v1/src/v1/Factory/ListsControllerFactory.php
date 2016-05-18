<?php
/**
 * Created by PhpStorm.
 * User: olcay
 * Date: 16.05.16
 * Time: 15:59
 */

    namespace v1\Factory;

    use Zend\ServiceManager\FactoryInterface;
    use Zend\ServiceManager\ServiceLocatorInterface;
    use v1\Controller\ListsController as ListsController;

    class ListsControllerFactory implements FactoryInterface
    {
        public function createService(ServiceLocatorInterface $serviceLocator)
        {
            $objectManager = $serviceLocator->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            return new ListsController($objectManager);
        }
    }