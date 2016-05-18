<?php
/**
 * Created by PhpStorm.
 * User: olcay
 * Date: 17.05.16
 * Time: 14:42
 */

    namespace v1\Controller;

    use Zend\View\Model\JsonModel;
    use Zend\Http\Response as HttpResponse;
    use Zend\Mvc\Controller\AbstractRestfulController;

    class TasksController extends AbstractRestfulController
    {

        protected $objectManager;

        public function __construct($objectManager)
        {

            $this->objectManager = $objectManager;

        }

        public function indexAction()
        {

            $method = $this->getRequest()->getMethod();

            switch ($method) {

                case 'GET':

                    $result = $this->getAllTasks();

                    if ($result && !empty($result)) {

                        return $response = new JsonModel(array(
                            'status' => true,
                            'collection' => $result,
                        ));

                    } else {

                        $this->getResponse()->setStatusCode(HttpResponse::STATUS_CODE_404);

                        return $response = new JsonModel(array(
                            'status' => false,
                            'message' => 'no_tasks_found',
                        ));

                    }

                break;

                case 'POST':

                    $result = $this->addTask();

                    if ($result) {

                        $this->getResponse()->setStatusCode(HttpResponse::STATUS_CODE_201);

                        return $response = new JsonModel(array(
                            'status' => true,
                            'result' => $result,
                        ));

                    } else {

                        $this->getResponse()->setStatusCode(HttpResponse::STATUS_CODE_400);

                        return $response = new JsonModel(array(
                            'status' => false,
                            'message' => 'new_task_not_added',
                        ));

                    }

                break;

                case 'PUT':

                    $result = $this->updateTask();

                    if ($result) {

                        return $response = new JsonModel(array(
                            'status' => true,
                            'result' => $result,
                        ));

                    } else {

                        $this->getResponse()->setStatusCode(HttpResponse::STATUS_CODE_400);

                        return $response = new JsonModel(array(
                            'status' => false,
                            'message' => 'task_not_updated',
                        ));

                    }

                break;

                case 'DELETE':

                    $result = $this->delTask();

                    if ($result) {

                        $this->getResponse()->setStatusCode(HttpResponse::STATUS_CODE_204);

                        return $response = new JsonModel(array(
                            'status' => true,
                            'message' => 'task_deleted',
                        ));

                    } else {

                        $this->getResponse()->setStatusCode(HttpResponse::STATUS_CODE_400);

                        return $response = new JsonModel(array(
                            'status' => false,
                            'message' => 'task_not_deleted',
                        ));

                    }

                break;

                default:
                    $this->getAllTasks();
            }
        }

        protected function getAllTasks() {

            $listID = $this->params()->fromRoute('id', 0);
            $taskID = $this->params()->fromRoute('taskid', 0);

            $criteria = array('list_id' => $listID);

            if($taskID > 0) {
                $criteria['id'] = $taskID;
            }

            $tasks = $this->objectManager
                ->getRepository('\v1\Entity\Tasks')
                ->findBy($criteria);

            if($tasks) {
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

            return false;

        }

        protected function addTask() {

            $listID = $this->params()->fromRoute('id', 0);

            $description = $this->params()->fromPost('description', null);
            $completed = $this->params()->fromPost('completed', 0);

            if($description) {

                $task = new \v1\Entity\Tasks();
                $task->setId();
                $task->setCompleted($completed);
                $task->setDescription($description);
                $task->setListId($listID);
                $this->objectManager->persist($task);
                $this->objectManager->flush();

                $taskToArray = array(
                    'id' => $task->getId(),
                    'description' => $task->getDescription(),
                    'completed' => $task->getCompleted(),
                    'list_id' => $task->getListId(),
                );

                return $taskToArray;

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

                $list = $this->objectManager
                    ->getRepository('\v1\Entity\Tasks')
                    ->findOneBy($criteria);

                if($list) {

                    isset($completed) ? $list->setCompleted($completed) : '';
                    isset($newDescription) ? $list->setDescription($newDescription) : '';

                    $this->objectManager->persist($list);
                    $this->objectManager->flush();

                    $taskToArray = array(
                        'description' => $list->getDescription(),
                        'completed' => $list->getCompleted()
                    );

                    return $taskToArray;
                }

                return false;

            }

            return false;

        }

        protected function delTask() {

            $listID = $this->params()->fromRoute('id', 0);
            $taskID = $this->params()->fromRoute('taskid', 0);

            if($listID > 0) {

                $criteria = array('id' => $taskID, 'list_id' => $listID);

                $list = $this->objectManager
                    ->getRepository('\v1\Entity\Tasks')
                    ->findOneBy($criteria);

                if($list) {
                    $this->objectManager->remove($list);
                    $this->objectManager->flush();

                    return true;
                }

                return false;
            }

            return false;

        }
    }