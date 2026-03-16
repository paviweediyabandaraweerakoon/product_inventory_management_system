<?php
namespace App\Controllers;

use App\Core\Controller;

class DashboardController extends Controller {
    
    public function index() {
        // Check if user is authenticated
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        return $this->view('dashboard/index');
    }
}