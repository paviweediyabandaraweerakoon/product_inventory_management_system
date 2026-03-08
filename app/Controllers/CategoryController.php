<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Requests\CategoryRequest;
use Exception;

/**
 * Class CategoryController
 * Handles all category-related operations including listing, creating, updating, and deleting.
 * * @package App\Controllers
 */
class CategoryController extends Controller
{
    /**
     * @var Category The category model instance for database operations.
     */
    private Category $categoryModel;

    /**
     * CategoryController constructor.
     * Initializes the Category model to be reused across all methods.
     */
    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = new Category();
    }

    /**
     * Display a listing of all categories.
     * * @return void
     */
    public function index(): void
    {
        try {
            $categories = $this->categoryModel->getAll();
            $this->view('categories/index', ['categories' => $categories]);
        } catch (Exception $e) {
            error_log("Error in Category index: " . $e->getMessage());
            header('Location: /dashboard?error=system_error');
        }
    }

    /**
     * Show the form for creating a new category.
     * * @return void
     */
    public function create(): void
    {
        $this->view('categories/create');
    }

    /**
     * Store a newly created category in the database.
     * * @return void
     */
    public function store(): void
    {
        // Early Return: Only process POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        try {
            // Assign and sanitize data in a single step
            $data = array_map(fn($v) => htmlspecialchars(trim((string)$v)), $_POST);
            
            $request = new CategoryRequest();

            // Validate using sanitized data
            if (!$request->validate($data)) {
                header('Location: /categories/create?error=invalid');
                exit;
            }

            $result = $this->categoryModel->create([
                'category_name' => $data['category_name'],
                'description'   => $data['description'] ?? '',
                'status'        => $data['status'] ?? 'active',
                'created_by'    => $_SESSION['user_id'] ?? 1
            ]);

            // Check return value before success alert
            if ($result) {
                header('Location: /categories?success=created');
            } else {
                throw new Exception("Database insertion failed");
            }
        } catch (Exception $e) {
            error_log("Error in Category store: " . $e->getMessage());
            header('Location: /categories/create?error=db_error');
        }
        exit;
    }

    /**
     * Show the form for editing an existing category.
     * * @param int $id The ID of the category.
     * @return void
     */
    public function edit(int $id): void
    {
        try {
            $category = $this->categoryModel->find($id);

            if (!$category) {
                header('Location: /categories?error=not_found');
                exit;
            }

            $this->view('categories/edit', ['category' => $category]);
        } catch (Exception $e) {
            error_log("Error in Category edit: " . $e->getMessage());
            header('Location: /categories?error=system_error');
        }
    }

    /**
     * Update the specified category in the database.
     * * @param int $id The ID of the category.
     * @return void
     */
    public function update(int $id): void
    {
        // Early Return
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        try {
            $data = array_map(fn($v) => htmlspecialchars(trim((string)$v)), $_POST);
            
            $request = new CategoryRequest();
            if (!$request->validate($data)) {
                header("Location: /categories/edit/$id?error=invalid");
                exit;
            }

            $result = $this->categoryModel->update($id, [
                'category_name' => $data['category_name'],
                'description'   => $data['description'] ?? '',
                'status'        => $data['status']
            ]);

            if ($result) {
                header('Location: /categories?success=updated');
            } else {
                throw new Exception("Update failed");
            }
        } catch (Exception $e) {
            error_log("Error in Category update: " . $e->getMessage());
            header("Location: /categories/edit/$id?error=update_fail");
        }
        exit;
    }

    /**
     * Soft delete the specified category (Renamed to destroy for REST principles).
     * * @param int $id The ID of the category.
     * @return void
     */
    public function destroy(int $id): void
    {
        try {
            $result = $this->categoryModel->delete($id);
            
            if ($result) {
                header('Location: /categories?success=deleted');
            } else {
                throw new Exception("Delete failed");
            }
        } catch (Exception $e) {
            error_log("Error in Category destroy: " . $e->getMessage());
            header('Location: /categories?error=delete_fail');
        }
        exit;
    }
}