<?php
/**
 * Created by PhpStorm.
 * User: olcay
 * Date: 17.05.16
 * Time: 14:41
 */

    namespace v1\Controller;

    use Zend\View\Model\JsonModel;
    use Zend\Http\Response as HttpResponse;
    use v1\Controller\UsersController as Users;
    use Zend\Mvc\Controller\AbstractRestfulController;

    class ListsController extends AbstractRestfulController
    {

        protected $userID = 0;
        protected $objectManager;

        public function __construct($objectManager)
        {

            $this->objectManager = $objectManager;

            $users = new Users($this->objectManager);
            $this->userID = $users->getUserIdByUsername($_SERVER['PHP_AUTH_USER']);

        }


        public function indexAction()
        {

            $method = $this->getRequest()->getMethod();

            switch ($method) {

                case 'GET':

                    $result = $this->getAllLists();

                    if($result && !empty($result)) {

                        return $response = new JsonModel(array(
                            'status' => true,
                            'result'=> $result,
                        ));

                    } else {

                        $this->getResponse()->setStatusCode(HttpResponse::STATUS_CODE_404);

                        return $response = new JsonModel(array(
                            'status' => false,
                            'message'=> 'no_lists_found',
                        ));
                    }

                break;

                case 'POST':

                    $result = $this->addList();

                    if($result) {

                        $this->getResponse()->setStatusCode(HttpResponse::STATUS_CODE_201);

                        return $response = new JsonModel(array(
                            'status' => true,
                            'result'=> $result,
                        ));

                    } else {

                        $this->getResponse()->setStatusCode(HttpResponse::STATUS_CODE_400);

                        return $response = new JsonModel(array(
                            'status' => false,
                            'message'=> 'new_list_not_added',
                        ));
                    }

                break;

                case 'PUT':

                    $result = $this->updateList();

                    if($result) {
                        return $response = new JsonModel(array(
                            'status' => true,
                            'result'=> $result,
                        ));
                    } else {

                        $this->getResponse()->setStatusCode(HttpResponse::STATUS_CODE_400);

                        return $response = new JsonModel(array(
                            'status' => false,
                            'message'=> 'list_not_updated',
                        ));
                    }

                break;

                case 'DELETE':

                    $result = $this->delList();

                    if($result) {

                        $this->getResponse()->setStatusCode(HttpResponse::STATUS_CODE_204);

                        return $response = new JsonModel(array(
                            'status' => true,
                            'message'=> 'list_deleted',
                        ));

                    } else {

                        $this->getResponse()->setStatusCode(HttpResponse::STATUS_CODE_400);

                        return $response = new JsonModel(array(
                            'status' => false,
                            'message'=> 'list_not_deleted',
                        ));

                    }

                break;

                default:
                    $this->getAllLists();
            }

        }

        protected function getAllLists()
        {

            $listID = $this->params()->fromRoute('id', 0);
            $criteria = array('user_id' => $this->userID);

            if($listID > 0) {
                $criteria['id'] = $listID;
            }

            $lists = $this->objectManager
                ->getRepository('\v1\Entity\Lists')
                ->findBy($criteria);

            $listsToArray = array();

            foreach($lists as $i) {
                $listsToArrayItem = array(
                    'id' => $i->getId(),
                    'name' => $i->getName(),
                    'description' => $i->getDescription()
                );

                $listsToArray[] = $listsToArrayItem;
            }

            return $listsToArray;
        }

        protected function addList()
        {

            $name = $this->params()->fromPost('name', null);
            $description = $this->params()->fromPost('description', null);

            if($name && $description) {

                $list = new \v1\Entity\Lists();
                $list->setId();
                $list->setName($name);
                $list->setDescription($description);
                $list->setUserId($this->userID);
                $this->objectManager->persist($list);
                $this->objectManager->flush();

                $listToArrayItem = array(
                    'id' => $list->getId(),
                    'name' => $list->getName(),
                    'description' => $list->getDescription()
                );

                return $listToArrayItem;

            }

            return false;

        }

        protected function updateList()
        {

            $listID = $this->params()->fromRoute('id', 0);
            $newName = $this->params()->fromQuery('name');
            $newDescription = $this->params()->fromQuery('description');

            $criteria = array('user_id' => $this->userID);

            if(($newName || $newDescription) && $listID > 0) {

                $criteria['id'] = $listID;

                $list = $this->objectManager
                    ->getRepository('\v1\Entity\Lists')
                    ->findOneBy($criteria);

                if($list) {

                    isset($newName) ? $list->setName($newName) : '';
                    isset($newDescription) ? $list->setDescription($newDescription) : '';

                    $this->objectManager->persist($list);
                    $this->objectManager->flush();

                    $listToArrayItem = array(
                        'id' => $list->getId(),
                        'name' => $list->getName(),
                        'description' => $list->getDescription()
                    );

                    return $listToArrayItem;
                }

                return false;

            }

            return false;

        }

        protected function delList()
        {

            $listID = $listID = $this->params()->fromRoute('id', 0);

            if($listID > 0) {

                $criteria = array('id' => $listID, 'user_id' => $this->userID);

                $list = $this->objectManager
                    ->getRepository('\v1\Entity\Lists')
                    ->findOneBy($criteria);

                if($list) {
                    try {
                        $this->objectManager->remove($list);
                    } catch(Exeption $e) {
                        return false;
                    }

                    $this->objectManager->flush();

                    return true;
                }

                return false;

            }

            return false;

        }

    }