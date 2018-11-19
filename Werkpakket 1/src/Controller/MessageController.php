<?php namespace App\Controller;

header("Access-Control-Allow-Origin: *");

use App\Model\Connection;
use App\Model\MessageModel;
use App\Model\PDOMessageModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    private $messageModel;
    
    public function __construct(MessageModel $messageModel)
    {
        $this->messageModel = $messageModel;
    }
    
    /**
    * @Route("/messages", methods={"GET"}, name="message")
    */
    public function getAllMessages()
    {
        $statusCode = 200;
        $messages = null;
        try {
            $messages = $this->messageModel->getAllMessages();
            if ($messages == null) {
                $statusCode = 404;
            }
        } catch (\Exception $exception) {
            $statusCode = 500;
        }
        return new JsonResponse($messages, $statusCode);
    }
    
    /**
    * @Route("/message/{id}", methods={"GET"}, name="getMessageById")
    */
    public function getMessage($id)
    {
        $statuscode = 200;
        
        $message = null;
        try {
            $message = $this->messageModel->getMessage($id);
            if ($message == null) {
                $statuscode = 404;
            }
        } catch (\InvalidArgumentException $exception) {
            $statuscode = 400;
        } catch (\PDOException $exception) {
            $statuscode = 500;
        }
        
        return new JsonResponse($message, $statuscode);
    }
    
    /**
    * @Route("/message", methods={"GET"}, name="getMessageByContentAndOrCategory")
    */
    public function findMessage(Request $request)
    {
        $statusCode = 200;
        $messages = null;

        try {
            $messages = $this->messageModel->findMessage($request->query->get('content'), $request->query->get('category'));
            if ($messages == null) {
                $statusCode = 404;
            }
        } catch (\InvalidArgumentException $exception) {
            $statusCode = 404;
        } catch (\PDOException $exception) {
            $statusCode = 500;
        }

        return new JsonResponse($messages, $statusCode);
    }
    
    /**
    * @Route("/message/upvote/{id}", methods={"POST"}, name="addUpvote")
    */
    public function addUpvote($id)
    {
        $statuscode = 200;
        
        $message = null;
        try {
            $message = $this->messageModel->addUpvote($id);
            if ($message == null) {
                $statuscode = 404;
            }
        } catch (\InvalidArgumentException $exception) {
            $statuscode = 400;
        } catch (\PDOException $exception) {
            $statuscode = 500;
        }
        
        return new JsonResponse($message, $statuscode);
    }
    
    /**
    * @Route("/message/downvote/{id}", methods={"POST"}, name="addDownvote")
    */
    public function addDownvote($id)
    {
        $statuscode = 200;
        
        $message = null;
        try {
            $message = $this->messageModel->addDownvote($id);
            if ($message == null) {
                $statuscode = 404;
            }
        } catch (\InvalidArgumentException $exception) {
            $statuscode = 400;
        } catch (\PDOException $exception) {
            $statuscode = 500;
        }
        
        return new JsonResponse($message, $statuscode);
    }
}
