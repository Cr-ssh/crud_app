<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Login - MyCRUD</title>
    <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
<div class="theme-toggle">
    <button id="themeToggleBtn">Switch to Dark Mode</button>
</div>
<div class="theme-toggle">
    <button id="themeToggleBtn">Switch to Dark Mode</button>
</div>

    <div class="container">
        <h2>Login</h2>
        <form action="../php/login_handler.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required />
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required />
            </div>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
<script>
    const toggleButton = document.getElementById("themeToggleBtn");
    const body = document.body;

    toggleButton.addEventListener("click", () => {
        body.classList.toggle("dark-mode");

        // Optionally toggle light mode class
        if (!body.classList.contains("dark-mode")) {
            body.classList.add("light-mode");
        } else {
            body.classList.remove("light-mode");
        }

        // Change button text
        toggleButton.innerText = body.classList.contains("dark-mode") ? "Switch to Light Mode" : "Switch to Dark Mode";
    });

    // Optional: persist mode using localStorage
    window.onload = () => {
        if (localStorage.getItem("theme") === "dark") {
            body.classList.add("dark-mode");
            toggleButton.innerText = "Switch to Light Mode";
        } else {
            body.classList.add("light-mode");
        }
    };

    // Save preference
    toggleButton.addEventListener("click", () => {
        localStorage.setItem("theme", body.classList.contains("dark-mode") ? "dark" : "light");
    });
</script>

</body>
</html>
