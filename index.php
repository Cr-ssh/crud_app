<?php include 'php/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
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
    <select id="category" required>
        <option value="">Select category</option>
        <option value="VAT">VAT</option>
        <option value="PAYE">PAYE</option>
        <option value="Withholding Tax">Withholding Tax</option>
        <option value="Corporate Tax">Corporate Tax</option>
    </select>
</div>

<div class="form-group">
    <label for="status">Status:</label>
    <select id="status" required>
        <option value="">Select status</option>
        <option value="Employed">Employed</option>
        <option value="Unemployed">Unemployed</option>
        <option value="Self-employed">Self-employed</option>
        <option value="Retired">Retired</option>
    </select>
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
                                <td data-label='Actions'>
                                    <button class='edit-btn' data-id='".htmlspecialchars($row['id'], ENT_QUOTES)."'>Edit</button>
                                    <button class='delete-btn' data-id='".htmlspecialchars($row['id'], ENT_QUOTES)."'>Delete</button>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No records found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
