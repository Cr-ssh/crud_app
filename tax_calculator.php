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
    <title>Tax Calculator</title>
</head>
<body>
    <h1>Tax Calculator</h1>
    
    <form id="tax-form">
        <label for="income">Enter Monthly Income (KES):</label>
        <input type="number" id="income" required>
        <button type="submit">Calculate</button>
    </form>

    <h3>Results:</h3>
    <p id="tax-output"></p>

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
            // Kenyan monthly PAYE tax brackets 2024 example
            let tax = 0;

            if(income <= 24000) {
                tax = income * 0.1;
            } else if(income <= 32333) {
                tax = (24000 * 0.1) + ((income - 24000) * 0.25);
            } else {
                tax = (24000 * 0.1) + ((32333 - 24000) * 0.25) + ((income - 32333) * 0.3);
            }

            // Personal relief (e.g. KES 2400/month)
            tax -= 2400;

            return tax > 0 ? tax : 0;
        }
    </script>
</body>
</html>
