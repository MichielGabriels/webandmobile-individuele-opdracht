<?php

namespace App\Controller;

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

        $sanitizedPageResults = $this->sanitize($pagedResults);

        return new JsonResponse($sanitizedPageResults, $statusCode);
    }

    /**
     * @Route("/users/{id}/remove", methods={"GET"}, name="removeUser")
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
     * @Route("/users/{id}/edit", methods={"GET"}, name="editUserRole")
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

        $sanitizedUser = $this->sanitize($user);

        return new JsonResponse($sanitizedUser, $statusCode);
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