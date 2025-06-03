<?php
session_start();
require_once 'php/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$name = '';
$id = '';
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT name FROM records WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($name);
        $stmt->fetch();
        $stmt->close();
    }
}

$taxType = isset($_GET['type']) ? strtolower($_GET['type']) : 'paye';
$validTypes = ['paye', 'withholding', 'personal'];
if (!in_array($taxType, $validTypes)) {
    $taxType = 'paye';
}

$income = isset($_GET['income']) ? floatval($_GET['income']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Calculator | KRA Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>

<main class="container">
    <div class="calculator-header">
        <h1 id="calculator-title">
            <i class="fas fa-calculator"></i> Tax Calculator: <?= strtoupper(htmlspecialchars($taxType)) ?>
        </h1>
        <p class="subtitle">Calculate your estimated tax liability</p>
    </div>

    <form id="tax-form" class="form-box">
        <input type="hidden" name="record_id" id="record-id" value="<?= $id ?>">
        
        <div class="form-group">
            <label for="calc-name">Calculation Name:</label>
            <input type="text" id="calc-name" name="calc-name" class="form-control" 
                   placeholder="e.g., April PAYE Calc" value="<?= htmlspecialchars($name) ?>">
        </div>

        <div class="form-group">
            <label for="tax-type">Tax Type:</label>
            <select id="tax-type" name="tax-type" class="form-control">
                <option value="paye" <?= $taxType === 'paye' ? 'selected' : '' ?>>PAYE</option>
                <option value="withholding" <?= $taxType === 'withholding' ? 'selected' : '' ?>>Withholding Tax</option>
                <option value="personal" <?= $taxType === 'personal' ? 'selected' : '' ?>>Personal Income Tax</option>
            </select>
        </div>

        <div class="form-group">
            <label for="income">Monthly Income (KES):</label>
            <div class="input-group">
                <span class="input-group-text">KES</span>
                <input type="number" id="income" name="income" class="form-control"
                       placeholder="e.g. 50000" required min="0" step="100"
                       value="<?= $income ?>" 
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            </div>
            <small class="form-text">Enter gross monthly income before deductions</small>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-calculator"></i> Calculate Tax
            </button>
        </div>
    </form>

    <section class="result-section" id="results" style="display: none;">
        <div class="result-header">
            <h3><i class="fas fa-chart-pie"></i> Tax Calculation Results</h3>
        </div>
        <div class="result-card">
            <div class="result-summary">
                <h4 id="tax-output">Estimated Tax: KES 0.00</h4>
                <div class="tax-details">
                    <div class="detail-item">
                        <span>Tax Type:</span>
                        <span id="result-type">PAYE</span>
                    </div>
                    <div class="detail-item">
                        <span>Monthly Income:</span>
                        <span id="result-income">KES 0.00</span>
                    </div>
                    <div class="detail-item">
                        <span>Tax Rate:</span>
                        <span id="result-rate">0%</span>
                    </div>
                </div>
            </div>
            <div id="tax-breakdown"></div>
        </div>
        <div class="result-actions">
            <button id="save-btn" class="btn btn-outline-primary">
                <i class="fas fa-save"></i> Save Calculation
            </button>
        </div>
    </section>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const urlParams = new URLSearchParams(window.location.search);
</script>
<script src="js/taxlogic.js"></script>
</body>
</html>
