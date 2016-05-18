<?php
/**
 * Created by PhpStorm.
 * User: olcay
 * Date: 9.05.16
 * Time: 19:12
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class RestController extends AbstractRestfulController
{

    protected $authStatus = false;
    protected $userID = 0;
    protected $objectManager;

    public function __construct($objectManager) {

        // Check username and password:
        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){

            $userName = $_SERVER['PHP_AUTH_USER'];
            $passWord = $_SERVER['PHP_AUTH_PW'];

            $criteria = array('username' => $userName, 'password' => $passWord);

            $this->objectManager = $objectManager;
            $user = $this->objectManager
                ->getRepository('\Application\Entity\Users')
                ->findOneBy($criteria);

            if($user && !empty($user)) {
                $this->authStatus = true;
                $this->userID = $user->getId();
            }
        }

    }

    public function indexAction() {

        if($this->authStatus == true) {

            $method = $this->getRequest()->getMethod();

            $uri = $_SERVER['REQUEST_URI'];
            $uriArr = explode('/',$uri);

            $process = 'lists';

            if(isset($uriArr[4]) && strpos($uriArr[4], "tasks") == 0) {
                $process = 'tasks';
            }

            switch($process) {
                case 'lists':
                    if($method == 'GET') {

                        $result = $this->getAllLists();

                        if($result && !empty($result)) {
                            return $response = new JsonModel(array(
                                'status' => true,
                                'collection'=> $result,
                            ));
                        } else {
                            return $response = new JsonModel(array(
                                'status' => false,
                                'message'=> 'no_lists_found',
                            ));
                        }

                    } else if($method == 'POST') {

                        $result = $this->addList();

                        if($result) {
                            return $response = new JsonModel(array(
                                'status' => true,
                                'message'=> 'new_list_added',
                            ));
                        } else {
                            return $response = new JsonModel(array(
                                'status' => false,
                                'message'=> 'new_list_not_added',
                            ));
                        }

                    } else if($method == 'PUT') {

                        $result = $this->updateList();

                        if($result) {
                            return $response = new JsonModel(array(
                                'status' => true,
                                'message'=> 'list_updated',
                            ));
                        } else {
                            return $response = new JsonModel(array(
                                'status' => false,
                                'message'=> 'list_not_updated',
                            ));
                        }

                    } else if($method == 'DELETE') {

                        $result = $this->delList();

                        if($result) {

                            header("HTTP/1.0 204 No Content");
                            $this->getResponse()->setStatusCode(204);

                            return $response = new JsonModel(array(
                                'status' => true,
                                'message'=> 'list_deleted',
                            ));
                        } else {
                            return $response = new JsonModel(array(
                                'status' => false,
                                'message'=> 'list_not_deleted',
                            ));
                        }
                    }
                break;


                case 'tasks':
                    if($method == 'GET') {

                        $result = $this->getAllTasks();

                        if($result && !empty($result)) {
                            return $response = new JsonModel(array(
                                'status' => true,
                                'collection'=> $result,
                            ));
                        } else {
                            return $response = new JsonModel(array(
                                'status' => false,
                                'message'=> 'no_tasks_found',
                            ));
                        }

                    } else if($method == 'POST') {

                        $result = $this->addTask();

                        if($result) {
                            return $response = new JsonModel(array(
                                'status' => true,
                                'message'=> 'new_task_added',
                            ));
                        } else {
                            return $response = new JsonModel(array(
                                'status' => false,
                                'message'=> 'new_task_not_added',
                            ));
                        }

                    } else if($method == 'PUT') {

                        $result = $this->updateTask();

                        if($result) {
                            return $response = new JsonModel(array(
                                'status' => true,
                                'message'=> 'task_updated',
                            ));
                        } else {
                            return $response = new JsonModel(array(
                                'status' => false,
                                'message'=> 'task_not_updated',
                            ));
                        }

                    } else if($method == 'DELETE') {

                        $result = $this->delTask();

                        if($result) {

                            header("HTTP/1.0 204 No Content");
                            $this->getResponse()->setStatusCode(204);

                            return $response = new JsonModel(array(
                                'status' => true,
                                'message'=> 'task_deleted',
                            ));
                        } else {
                            return $response = new JsonModel(array(
                                'status' => false,
                                'message'=> 'task_not_deleted',
                            ));
                        }
                    }
                break;

                default:
                    $this->getAllListsAction();
            }


        } else {
            header("WWW-Authenticate: Basic realm=\"CreaSoup Rest API\"");
            header("HTTP/1.0 401 Unauthorized");
            $this->getResponse()->setStatusCode(401);

            return new JsonModel(array(
                'status' => false,
                'message'=> 'login_error',
            ));
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
            ->getRepository('\Application\Entity\Lists')
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

    protected function addList() {

        $name = $this->params()->fromPost('name', null);
        $description = $this->params()->fromPost('description', null);

        if($name && $description) {

            try {
                $list = new \Application\Entity\Lists();
                $list->setId();
                $list->setName($name);
                $list->setDescription($description);
                $list->setUserId($this->userID);
                $this->objectManager->persist($list);
                $this->objectManager->flush();
            } catch(Exception $e) {
                return false;
            }

            return true;

        }

        return false;

    }

    protected function updateList() {

        $listID = $this->params()->fromRoute('id', 0);
        $newName = $this->params()->fromQuery('name');
        $newDescription = $this->params()->fromQuery('description');

        $criteria = array('user_id' => $this->userID);

        if(($newName || $newDescription) && $listID > 0) {

            $criteria['id'] = $listID;

            try {
                $list = $this->objectManager
                    ->getRepository('\Application\Entity\Lists')
                    ->findOneBy($criteria);

                isset($newName) ? $list->setName($newName) : '';
                isset($newDescription) ? $list->setDescription($newDescription) : '';

                $this->objectManager->persist($list);
                $this->objectManager->flush();
            } catch(Exception $e) {
                return false;
            }

            return true;
        }

        return false;

    }

    protected function delList() {

        $listID = $listID = $this->params()->fromRoute('id', 0);

        if($listID > 0) {

            $criteria = array('id' => $listID, 'user_id' => $this->userID);

            try {
                $list = $this->objectManager
                    ->getRepository('\Application\Entity\Lists')
                    ->findOneBy($criteria);

                $this->objectManager->remove($list);
                $this->objectManager->flush();
            } catch(\Exception $e) {
                return false;
            }

            return true;
        }

        return false;

    }

    protected function getAllTasks() {

        $listID = $this->params()->fromRoute('id', 0);
        $taskID = $this->params()->fromRoute('taskid', 0);

        $criteria = array('list_id' => $listID);

        if($taskID > 0) {
            $criteria['id'] = $taskID;
        }

        $tasks = $this->objectManager
            ->getRepository('\Application\Entity\Tasks')
            ->findBy($criteria);

        $tasksToArray = array();

        foreach($tasks as $i) {
            $tasksToArrayItem = array(
                'id' => $i->getId(),
                'description' => $i->getDescription(),
                'completed' => $i->getCompleted(),
                'list_id' => $i->getListId(),
            );

            $tasksToArray[] = $tasksToArrayItem;
        }

        return $tasksToArray;

    }

    protected function addTask() {

        $listID = $this->params()->fromRoute('id', 0);

        $description = $this->params()->fromPost('description', null);
        $completed = $this->params()->fromPost('completed', 0);

        if($description) {

            try {
                $list = new \Application\Entity\Tasks();
                $list->setId();
                $list->setCompleted($completed);
                $list->setDescription($description);
                $list->setListId($listID);
                $this->objectManager->persist($list);
                $this->objectManager->flush();
            } catch(Exception $e) {
                return false;
            }

            return true;

        }

        return false;

    }

    protected function updateTask() {

        $listID = $this->params()->fromRoute('id', 0);
        $taskId = $this->params()->fromRoute('taskid', 0);

        $newDescription = $this->params()->fromQuery('description');
        $completed = $this->params()->fromQuery('completed');

        if(($completed || $newDescription) && $listID > 0) {

            $criteria = array('id' => $taskId, 'list_id' => $listID);

            try {
                $list = $this->objectManager
                    ->getRepository('\Application\Entity\Tasks')
                    ->findOneBy($criteria);

                isset($completed) ? $list->setCompleted($completed) : '';
                isset($newDescription) ? $list->setDescription($newDescription) : '';

                $this->objectManager->persist($list);
                $this->objectManager->flush();
            } catch(Exception $e) {
                return false;
            }

            return true;
        }

        return false;

    }

    protected function delTask() {

        $listID = $this->params()->fromRoute('id', 0);
        $taskID = $this->params()->fromRoute('taskid', 0);

        if($listID > 0) {

            $criteria = array('id' => $taskID, 'list_id' => $listID);

            try {
                $list = $this->objectManager
                    ->getRepository('\Application\Entity\Tasks')
                    ->findOneBy($criteria);

                $this->objectManager->remove($list);
                $this->objectManager->flush();
            } catch(Exception $e) {
                return false;
            }

            return true;
        }

        return false;

    }

}