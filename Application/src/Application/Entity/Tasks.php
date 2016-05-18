<?php
/**
 * Created by PhpStorm.
 * User: olcay
 * Date: 16.05.16
 * Time: 05:50
 */


    namespace Application\Entity;

    use Doctrine\ORM\Mapping as ORM;

    /** @ORM\Entity */
    class Tasks
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="AUTO")
         * @ORM\Column(type="integer")
         */
        protected $id;

        /** @ORM\Column(type="string") */
        protected $description;

        /** @ORM\Column(type="integer") */
        protected $completed;

        /** @ORM\Column(type="integer") */
        protected $list_id;

        public function getId() {
            return $this->id;
        }

        public function setId() {
            return $this->id;
        }

        public function getDescription() {
            return $this->description;
        }

        public function setDescription($description) {
            $this->description = $description;
            return $this;
        }

        public function getCompleted() {
            return $this->completed;
        }

        public function setCompleted($completed) {
            $this->completed = $completed;
            return $this;
        }

        public function getListId() {
            return $this->list_id;
        }

        public function setListId($list_id) {
            $this->list_id = $list_id;
            return $this;
        }

    }