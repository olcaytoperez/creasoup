<?php
/**
 * Created by PhpStorm.
 * User: olcay
 * Date: 10.05.16
 * Time: 13:03
 */


    namespace Application\Entity;

    use Doctrine\ORM\Mapping as ORM;

    /** @ORM\Entity */
    class Users
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="AUTO")
         * @ORM\Column(type="integer")
         */
        protected $id;

        /** @ORM\Column(type="string") */
        protected $username;

        /** @ORM\Column(type="string") */
        protected $password;

        public function getId(){
            return $this->id;
        }

        public function getUserName(){
            return $this->username;
        }

        public function getPassword(){
            return $this->password;
        }

    }