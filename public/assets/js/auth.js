document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    // Handle Login
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(loginForm);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch('api/login_endpoint.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();

                if (result.success) {
                    localStorage.setItem('inventoryToken', result.data.token);
                    window.location.href = 'dashboard.php';
                } else {
                    alert(result.message); // Replace with your local toast component
                }
            } catch (err) {
                console.error("Login Error:", err);
            }
        });
    }

    // Handle Register
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(registerForm);
            const data = Object.fromEntries(formData.entries());

            // Simple Password Match Check
            if (data.password !== data.confirmPassword) {
                alert("Passwords do not match!");
                return;
            }

            try {
                const response = await fetch('api/register_endpoint.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                if (result.success) window.location.href = 'login.php';
            } catch (err) {
                console.error("Register Error:", err);
            }
        });
    }
});