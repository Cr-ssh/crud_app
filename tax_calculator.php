<?php include 'navbar.php'; ?>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tax Calculator</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <img src="img/kra-logo.png" alt="KRA Logo" height="60">
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="taxpayers.php">Taxpayer Management</a>
            <a href="tax_calculator.php">Tax Calculator</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <h1>Tax Calculator</h1>
        <form id="tax-form">
            <label for="income">Enter Monthly Income (KES):</label>
            <input type="number" id="income" required>
            <button type="submit">Calculate</button>
        </form>

        <h3>Results:</h3>
        <p id="tax-output"></p>
    </main>

    <script>
        const form = document.getElementById('tax-form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const income = parseFloat(document.getElementById('income').value);
            if (income < 0) {
                alert('Income cannot be negative');
                return;
            }
            let tax = calculateTax(income);
            document.getElementById('tax-output').textContent = `Estimated Tax: KES ${tax.toFixed(2)}`;
        });

        function calculateTax(income) {
            let tax = 0;
            if (income <= 24000) {
                tax = income * 0.1;
            } else if (income <= 32333) {
                tax = (24000 * 0.1) + ((income - 24000) * 0.25);
            } else {
                tax = (24000 * 0.1) + ((32333 - 24000) * 0.25) + ((income - 32333) * 0.3);
            }
            tax -= 2400; // personal relief
            return tax > 0 ? tax : 0;
        }
    </script>
</body>
</html>