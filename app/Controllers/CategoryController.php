<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Requests\CategoryRequest; // Class for Validation

class CategoryController extends Controller {
    
    // 1. show all categories
    public function index() {
        $model = new Category();
        $categories = $model->getAll();
        return $this->view('categories/index', ['categories' => $categories]);
    }

    // 2. Show create category form
    public function create() {
        return $this->view('categories/create');
    }

    // 3. Save new category to database
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $request = new CategoryRequest();
            
            // Validation Check
            if (!$request->validate($_POST)) {
                header('Location: /categories/create?error=invalid');
                exit;
            }

            $model = new Category();
            $model->create([
                'category_name' => htmlspecialchars(trim($_POST['category_name'])),
                'description'   => htmlspecialchars(trim($_POST['description'] ?? '')),
                'status'        => $_POST['status'] ?? 'active',
                'created_by'    => $_SESSION['user_id'] ?? 1 // temporary user_id for created_by field, replace with actual user session data in real implementation
            ]);

            header('Location: /categories?success=created');
            exit;
        }
    }

    // 4. Show edit form with existing category data
    public function edit($id) {
        $model = new Category();
        $category = $model->find($id);

        if (!$category) {
            header('Location: /categories?error=not_found');
            exit;
        }
        // Send $category array to the view
        return $this->view('categories/edit', ['category' => $category]);
    }

    // 5. Update category data in database
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $request = new CategoryRequest();

            // Validation Check
            if (!$request->validate($_POST)) {
                header("Location: /categories/edit/$id?error=invalid");
                exit;
            }

            $model = new Category();
            $model->update($id, [
                'category_name' => htmlspecialchars(trim($_POST['category_name'])),
                'description'   => htmlspecialchars(trim($_POST['description'] ?? '')),
                'status'        => $_POST['status']
            ]);

            header('Location: /categories?success=updated');
            exit;
        }
    }

    // 6. Soft delete category
    public function delete($id) {
        $model = new Category();
        $model->delete($id); 
        header('Location: /categories?success=deleted');
        exit;
    }
}