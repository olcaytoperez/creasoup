<?php
/**
 * Created by PhpStorm.
 * User: olcay
 * Date: 18.05.16
 * Time: 07:57
 */

    namespace v1\Controller;

    use Zend\Mvc\Controller\AbstractRestfulController;

    class UsersController extends AbstractRestfulController
    {

        protected $objectManager;

        public function __construct($objectManager)
        {
            $this->objectManager = $objectManager;
        }

        public function getUserIdByUsername($username)
        {
            $criteria = array('username' => $username);

            $user = $this->objectManager
                ->getRepository('\v1\Entity\Users')
                ->findOneBy($criteria);

            return $user->getId();
        }
    }