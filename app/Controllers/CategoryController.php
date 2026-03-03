<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;

class CategoryController extends Controller {
    
    // 1. View all categories
    public function index() {
        $model = new Category();
        $categories = $model->getAll();
        return $this->view('categories/index', ['categories' => $categories]);
    }

    // 2. Show Create Form
    public function create() {
        return $this->view('categories/create');
    }

    // 3. Store New Category (with Validation)
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new Category();

            // --- Server-side Validation---
            $name = trim($_POST['category_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $status = $_POST['status'] ?? 'active';

            if (empty($name) || strlen($name) < 3) {
                // In real world, this one back with error message.
                header('Location: /categories/create?error=name_too_short');
                exit;
            }

            $model->create([
                'category_name' => htmlspecialchars($name),
                'description'   => htmlspecialchars($description),
                'status'        => $status,
                'created_by'    => $_SESSION['user_id'] ?? 1 // no Session, this temperary
            ]);

            header('Location: /categories?success=created');
            exit;
        }
    }

    // 4. Show Edit Form
    public function edit($id) {
        $model = new Category();
        $category = $model->find($id);

        if (!$category) {
            header('Location: /categories?error=not_found');
            exit;
        }

        return $this->view('categories/edit', ['category' => $category]);
    }

    // 5. Update Existing Category
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new Category();

            // Validation
            $name = trim($_POST['category_name'] ?? '');
            if (empty($name) || strlen($name) < 3) {
                header("Location: /categories/edit/$id?error=invalid");
                exit;
            }

            $model->update($id, [
                'category_name' => htmlspecialchars($name),
                'description'   => htmlspecialchars($_POST['description']),
                'status'        => $_POST['status']
            ]);

            header('Location: /categories?success=updated');
            exit;
        }
    }

    // 6. Delete Category (Soft Delete or Hard Delete)
    public function delete($id) {
        $model = new Category();
        // Soft delete - set deleted_at timestamp (if implemented in model)
        $model->delete($id); 
        
        header('Location: /categories?success=deleted');
        exit;
    }
}