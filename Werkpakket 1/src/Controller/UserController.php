<?php

namespace App\Controller;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, PUT, GET, OPTIONS, DELETE");

use App\Model\UserModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;
use PagerFanta\Exception\NotValidMaxPerPageException;
use PagerFanta\Exception\NotValidCurrentPageException;
use Waavi\Sanitizer\Sanitizer;

class UserController extends AbstractController
{
    private $userModel;

    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    /**
     * @Route("/users", methods={"GET"}, name="getAllUsers")
     */
    public function getAllUsers()
    {
        $statusCode = 200;

        $users = null;
        try {
            $users = $this->userModel->getAllUsers();
            if ($users == null) {
                $statusCode = 404;
            }
        } catch (\Exception $exception) {
            $statusCode = 500;
        }

        $pagedResults = null;
        try {
            $pagedResults = $this->getPagedResults($users, $this->getItemsPerPage(), $this->getPage());
        } catch (\Exception $exception) {
            $statusCode = 400;
        }

        $sanitizedPageResults = null;
        if ($pagedResults != null) {
            $sanitizedPageResults = $this->sanitize($pagedResults);
        } else {
            $statusCode = 400;
        }

        return new JsonResponse($sanitizedPageResults, $statusCode);
    }

    /**
     * @Route("/users/remove/{id}", methods={"DELETE", "OPTIONS"}, name="removeUser")
     */
    public function removeUser($id)
    {
        $statusCode = 200;

        try {
            $this->userModel->removeUser($id);
        } catch (\InvalidArgumentException $exception) {
            $statusCode = 404;
        } catch (\Exception $exception) {
            $statusCode = 500;
        }

        return new JsonResponse(null, $statusCode);
    }

    /**
     * @Route("/users/edit/{id}", methods={"PUT", "OPTIONS"}, name="editUserRole")
     */
    public function editUserRole($id)
    {
        $statusCode = 200;

        $newRole = $this->getNewUserRole();
        if ($newRole == null) {
            return new JsonResponse(null, 400);
        }

        $user = null;
        try {
            $user = $this->userModel->editUserRole($id, $newRole);
            if ($user == null) {
                $statusCode = 404; // user with updated role not found
            }
        } catch (\InvalidArgumentException $exception) {
            $statusCode = 404; // user with {id} not found
        } catch (\Exception $exception) {
            $statusCode = 500;
        }

        $sanitizedUser = null;
        if ($user != null) {
            $sanitizedUser = $this->sanitize($user);
        } else {
            $statusCode = 400;
        }

        return new JsonResponse($sanitizedUser, $statusCode);
    }

    /**
     * @Route("/user/login", methods={"GET"}, name="loginUser")
     */
    public function login()
    {
        $statusCode = 200;

        $username = $this->getLoginUsername();
        if ($username == null) {
            return new JsonResponse(null, 400);
        }

        $password = $this->getLoginPassword();
        if ($password == null) {
            return new JsonResponse(null, 400);
        }

        $loggedInUser = null;
        try {
            $loggedInUser = $this->userModel->loginUser($username, $password);
            if ($loggedInUser == null) {
                $statusCode = 404; // logged in user not found
            }
        } catch (\InvalidArgumentException $exception) {
            $statusCode = 404; // user with 'username' not found
        } catch (\Exception $exception) {
            $statusCode = 500;
        }

        $sanitizedLoggedInUser = null;
        if ($loggedInUser != null) {
            $sanitizedLoggedInUser = $this->sanitize($loggedInUser);
        }

        return new JsonResponse($sanitizedLoggedInUser, $statusCode);
    }

    private function getItemsPerPage()
    {
        $itemsPerPage = null;
        if (isset($_GET['itemsPerPage'])) {
            $itemsPerPage = $_GET['itemsPerPage'];
        }

        return $itemsPerPage;
    }

    private function getPage()
    {
        $page = null;
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        }

        return $page;
    }

    private function getPagedResults($users, $itemsPerPage, $page)
    {
        $adapter = new ArrayAdapter($users);
        $pagerfanta = new Pagerfanta($adapter);

        // 10 by default
        if ($itemsPerPage != null) {
            try {
                $pagerfanta->setMaxPerPage($itemsPerPage);
            } catch (NotValidMaxPerPageException $exception) {
                throw new $exception;
            }
        }

        // 1 by default
        if ($page != null) {
            try {
                $pagerfanta->setCurrentPage($page);
            } catch (NotValidCurrentPageException $exception) {
                throw new $exception;
            }
        }

        $currentPageResults = $pagerfanta->getCurrentPageResults();

        return $currentPageResults;
    }

    private function getNewUserRole()
    {
        $newRole = null;
        if (isset($_GET['role'])) {
            $newRole = $_GET['role'];
        }

        return $newRole;
    }

    private function getLoginUsername()
    {
        $username = null;
        if (isset($_GET['username'])) {
            $username = $_GET['username'];
        }

        return $username;
    }

    private function getLoginPassword()
    {
        $password = null;
        if (isset($_GET['password'])) {
            $password = $_GET['password'];
        }

        return $password;
    }

    private function sanitize($data)
    {
        $sanitizedData = array();

        $customFilters = [
            'hash' => function($value, $options = []) {
                    return sha1($value);
                },
            'remove_whitespace' => RemoveWhitespaceFilter::class
        ];

        $filters = [
            'username' => 'remove_whitespace|strip_tags|capitalize',
            'role' => 'remove_whitespace|strip_tags|capitalize'
        ];

        // Check if $data is a multidimensional array
        if (count($data) != count($data, COUNT_RECURSIVE)) {
            foreach ($data as $array) {
                $sanitizer = new Sanitizer($array, $filters, $customFilters);
                array_push($sanitizedData, $sanitizer->sanitize());
            }
        } else {
            $sanitizer = new Sanitizer($data, $filters, $customFilters);
            $sanitizedData = $sanitizer->sanitize();
        }

        return $sanitizedData;
    }
}