<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Role;
use App\Requests\UserRequest;
use Throwable;

/**
 * Class UserController
 * Handles user lifecycle management including listing, registration,
 * profile updates, and role-based access control.
 */
class UserController extends Controller
{
    private User $userModel;
    private Role $roleModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->roleModel = new Role();
    }

    /**
     * Display a listing of all registered users with their metrics.
     */
    public function index(): void
    {
        try {
            $users = $this->userModel->getAllUsers();
            
            $data = [
                'users' => $users,
                'stats' => [
                    'total'   => count($users),
                    'active'  => count(array_filter($users, fn($u) => ($u['status'] ?? 0) == 1)),
                    'admins'  => count(array_filter($users, fn($u) => strtolower($u['role_name'] ?? '') === 'admin')),
                    'staff'   => count(array_filter($users, fn($u) => strtolower($u['role_name'] ?? '') !== 'admin'))
                ],
                'title' => 'User Management'
            ];

            $this->view('users/index', $data);

        } catch (Throwable $e) {
            $this->logError('User Index', $e);
            $this->handleServerError();
        }
    }

    /**
     * Show form for creating a new user profile.
     */
    public function create(): void
    {
        try {
            $this->view('users/create', [
                'roles'  => $this->roleModel->all(),
                'title'  => 'Add New User',
                'errors' => [],
                'old'    => []
            ]);
        } catch (Throwable $e) {
            $this->logError('User Create View', $e);
            $this->handleServerError();
        }
    }

    /**
     * Store a newly created user after strict validation.
     */
    public function store(): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/users');
                return;
            }

            $data = $this->getPostData();
            $request = new UserRequest($data);

            if (!$request->validate()) {
                $this->view('users/create', [
                    'errors' => $request->getErrors(),
                    'roles'  => $this->roleModel->all(),
                    'old'    => $data,
                    'title'  => 'Add New User'
                ]);
                return;
            }

            // User creation logic
            if ($this->userModel->createUser($data)) {
                $this->redirect('/users?success=user_created');
            } else {
                throw new \Exception("Execution failed during user creation.");
            }

        } catch (Throwable $e) {
            $this->logError('User Store', $e);
            $this->handleServerError();
        }
    }

    /**
     * Show form for editing an existing user.
     * * @param int $id
     */
    public function edit(int $id): void
    {
        try {
            $user = $this->userModel->find($id);
            if (!$user) {
                $this->redirect('/users?error=user_not_found');
                return;
            }

            $this->view('users/edit', [
                'user'   => $user,
                'roles'  => $this->roleModel->all(),
                'title'  => 'Edit User Profile',
                'errors' => []
            ]);
        } catch (Throwable $e) {
            $this->logError('User Edit View', $e);
            $this->handleServerError();
        }
    }

    /**
     * Update user details in storage.
     * * @param int $id
     */
    public function update(int $id): void
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect('/users');
                return;
            }

            $data = $this->getPostData();
            $request = new UserRequest($data);

            // Update mode true ලෙස pass කරනවා
            if (!$request->validate(true)) {
                $this->view('users/edit', [
                    'errors' => $request->getErrors(),
                    'user'   => array_merge(['id' => $id], $data),
                    'roles'  => $this->roleModel->all(),
                    'title'  => 'Edit User Profile'
                ]);
                return;
            }

            if ($this->userModel->updateUser($id, $data)) {
                $this->redirect('/users?success=user_updated');
            } else {
                $this->redirect('/users?error=update_failed');
            }

        } catch (Throwable $e) {
            $this->logError('User Update', $e);
            $this->handleServerError();
        }
    }

    /**
     * Perform a soft delete on a user record.
     * * @param int $id
     */
    public function delete(int $id): void
    {
        try {
            $user = $this->userModel->find($id);
            if (!$user) {
                $this->redirect('/users?error=not_found');
                return;
            }

            if ($this->userModel->delete($id)) {
                $this->redirect('/users?success=user_deleted');
            } else {
                $this->redirect('/users?error=delete_failed');
            }
        } catch (Throwable $e) {
            $this->logError('User Delete', $e);
            $this->handleServerError();
        }
    }

    /**
     * Private helper to streamline 500 error responses.
     */
    private function handleServerError(): void
    {
        http_response_code(500);
        $this->view('errors/500');
        exit;
    }
}