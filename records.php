<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include 'php/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="light-mode">
    <!-- Theme Toggle Button -->
    <div class="theme-toggle">
        <button id="themeToggleBtn">Switch to Dark Mode</button>
    </div>

    <div class="container">
        <h1>Records Management</h1>
        
        <!-- Add/Edit Record Form -->
        <div class="form-container">
            <h2 id="form-title">Add New Record</h2>
            <form id="record-form" method="POST" action="php/handle_form.php">
                <input type="hidden" name="id" id="record-id">
                
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required placeholder="Enter full name">
                </div>
                <div class="form-group">
                    <label for="kra_pin">KRA Pin:</label>
                    <input type="text" id="kra_pin" name="kra_pin" placeholder="Enter KRA Pin">
                </div>

                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category" required>
                        <option value="">Select category</option>
                        <option value="VAT">VAT</option>
                        <option value="PAYE">PAYE</option>
                        <option value="Withholding Tax">Withholding Tax</option>
                        <option value="Corporate Tax">Corporate Tax</option>
                    </select>
                    <div id="category-info" class="calculation-font" style="margin-top: 10px; color: #b30000;"></div>
                </div>

                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="">Select status</option>
                        <option value="Employed">Employed</option>
                        <option value="Self-employed">Self-employed</option>
                    </select>
                    <div id="status-info" class="calculation-font" style="margin-top: 10px; color: #b30000;"></div>
                </div>

                <button type="submit" id="submit-btn" name="submit">Submit</button>
                <button type="button" id="cancel-btn" style="display:none;">Cancel</button>
            </form>
        </div>

        <!-- Records Table -->
        <div class="table-container">
            <table id="records-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>KRA Pin</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Tax Amount (KES)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM records";
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr data-id='".htmlspecialchars($row['id'], ENT_QUOTES)."'>
                                <td data-label='ID'>".htmlspecialchars($row['id'], ENT_QUOTES)."</td>
                                <td data-label='Name'>".htmlspecialchars($row['name'], ENT_QUOTES)."</td>
                                <td data-label='KRA Pin'>".htmlspecialchars($row['kra_pin'], ENT_QUOTES)."</td>
                                <td data-label='Category'>".htmlspecialchars($row['category'], ENT_QUOTES)."</td>
                                <td data-label='Status'>".htmlspecialchars($row['status'], ENT_QUOTES)."</td>
                                <td data-label='Tax Amount'>".number_format($row['tax_amount'], 2)."</td>
                                <td data-label='Actions'>
                                    <button class='edit-btn' data-id='".htmlspecialchars($row['id'], ENT_QUOTES)."'>Edit</button>
                                    <button class='delete-btn' data-id='".htmlspecialchars($row['id'], ENT_QUOTES)."'>Delete</button>
                                    <a href='tax_calculator.php?id=".htmlspecialchars($row['id'], ENT_QUOTES)."' style='text-decoration: none; margin-left: 5px;'>
                                        <button type='button'>Go to Tax Calculator</button>
                                    </a>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No records found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="js/script.js"></script>
    <script>
        // Category Message
        document.getElementById('category').addEventListener('change', function () {
            const infoBox = document.getElementById('category-info');
            const category = this.value;

            let message = '';
            switch (category) {
                case 'PAYE':
                    message = 'PAYE is remitted by employers on behalf of their employees.';
                    break;
                case 'Withholding Tax':
                    message = 'Withholding Tax is paid by the payer when making payments like rent or professional fees.';
                    break;
                case 'Corporate Tax':
                    message = 'Corporate Tax is paid directly by registered companies on their net profits.';
                    break;
                case 'VAT':
                    message = 'VAT is collected by registered businesses and remitted to KRA.';
                    break;
                default:
                    message = '';
            }

            infoBox.textContent = message;
        });

        // Status Message
        document.getElementById('status').addEventListener('change', function () {
            const infoBox = document.getElementById('status-info');
            const status = this.value;

            let message = '';
            switch (status) {
                case 'Employed':
                    message = 'Employed individuals typically pay PAYE through their employers.';
                    break;
                case 'Self-employed':
                    message = 'Self-employed persons file and pay taxes like VAT, Withholding, and Personal Income Tax themselves.';
                    break;
                default:
                    message = '';
            }

            infoBox.textContent = message;
        });

        // Theme Toggle Script
        const toggleButton = document.getElementById("themeToggleBtn");
        const body = document.body;

        toggleButton.addEventListener("click", () => {
            body.classList.toggle("dark-mode");

            if (body.classList.contains("dark-mode")) {
                body.classList.remove("light-mode");
                toggleButton.innerText = "Switch to Light Mode";
                localStorage.setItem("theme", "dark");
            } else {
                body.classList.add("light-mode");
                toggleButton.innerText = "Switch to Dark Mode";
                localStorage.setItem("theme", "light");
            }
        });

        window.onload = () => {
            const theme = localStorage.getItem("theme");
            if (theme === "dark") {
                body.classList.add("dark-mode");
                body.classList.remove("light-mode");
                toggleButton.innerText = "Switch to Light Mode";
            } else {
                body.classList.add("light-mode");
                body.classList.remove("dark-mode");
                toggleButton.innerText = "Switch to Dark Mode";
            }
        };
    </script>
</body>
</html>
