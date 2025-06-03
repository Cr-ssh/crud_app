<!-- navbar.php -->
<style>
    .navbar {
        background-color: #e60000; /* KRA Red */
        color: white;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-family: Arial, sans-serif;
    }

    .navbar .logo {
        display: flex;
        align-items: center;
    }

    .navbar .logo img {
        height: 40px;
        margin-right: 10px;
    }

    .navbar .logo span {
        font-size: 20px;
        font-weight: bold;
    }

    .navbar .nav-links a {
        color: white;
        text-decoration: none;
        margin-left: 25px;
        font-weight: bold;
        transition: color 0.3s ease;
    }

    .navbar .nav-links a:hover {
        color: #fdd835; /* A bright highlight on hover */
    }

    @media (max-width: 768px) {
        .navbar {
            flex-direction: column;
            align-items: flex-start;
        }

        .navbar .nav-links {
            margin-top: 10px;
        }

        .navbar .nav-links a {
            display: block;
            margin: 5px 0;
        }
    }
</style>

<div class="navbar">
    <div class="logo">
        <img src="img/KRA_Logo.png" alt="KRA Logo">
        <span>KRA Portal</span>
    </div>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_taxpayers.php">Taxpayers</a>
        <a href="tax_calculator.php">Tax Calculator</a>
        <a href="logout.php">Logout</a>
	<a href="tax_history.php">View Tax History</a>
	
    </div>
</div>
