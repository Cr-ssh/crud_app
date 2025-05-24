<?php
session_start();
require_once 'php/config.php'; // For database and other configurations

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Validate and sanitize tax type
$taxType = isset($_GET['type']) ? strtolower($_GET['type']) : 'paye';
$validTypes = ['paye', 'withholding', 'personal'];
if (!in_array($taxType, $validTypes)) {
    $taxType = 'paye';
}
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
                <i class="fas fa-calculator"></i> Tax Calculator: <?php echo htmlspecialchars(strtoupper($taxType)); ?>
            </h1>
            <p class="subtitle">Calculate your estimated tax liability</p>
        </div>

        <form id="tax-form" class="form-box">
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
                    <input type="number" id="income" name="income" 
                           class="form-control" 
                           placeholder="e.g. 50000" 
                           required 
                           min="0"
                           step="100">
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
                <button id="print-btn" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-print"></i> Print
                </button>
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
                
                <div class="breakdown-container">
                    <h5><i class="fas fa-list-ol"></i> Tax Breakdown</h5>
                    <div id="tax-breakdown" class="breakdown-content"></div>
                </div>
            </div>
            
            <div class="result-actions">
                <button id="save-btn" class="btn btn-outline-primary">
                    <i class="fas fa-save"></i> Save Calculation
                </button>
                <button id="history-btn" class="btn btn-outline-info">
                    <i class="fas fa-history"></i> View History
                </button>
            </div>
        </section>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
$(document).ready(function() {
    const form = $('#tax-form');
    const taxOutput = $('#tax-output');
    const breakdownElement = $('#tax-breakdown');
    const taxTypeSelect = $('#tax-type');
    const resultSection = $('#results');
    const resultType = $('#result-type');
    const resultIncome = $('#result-income');
    const resultRate = $('#result-rate');

    taxTypeSelect.change(function() {
        const selected = $(this).val();
        window.location.href = `tax_calculator.php?type=${selected}`;
    });

    form.submit(function(e) {
        e.preventDefault();
        const income = parseFloat($('#income').val());
        const taxType = taxTypeSelect.val();

        if (isNaN(income)) {
            showError("Please enter a valid income amount");
            return;
        }

        if (income < 0) {
            showError("Income cannot be negative");
            return;
        }

        calculateTax(income, taxType);
    });

    $('#print-btn').click(function() {
        window.print();
    });

    function calculateTax(income, taxType) {
        let result = {
            tax: 0,
            breakdown: "",
            rate: "0%",
            typeName: taxType.toUpperCase()
        };

        switch (taxType) {
            case 'paye':
                result = calculatePAYE(income);
                break;
            case 'withholding':
                result.tax = income * 0.05;
                result.breakdown = generateBreakdown([
                    { label: "Withholding Tax Rate", value: "5%" },
                    { label: "Taxable Income", value: formatCurrency(income) },
                    { label: "Tax Calculation", value: `5% × ${formatCurrency(income)}` },
                    { label: "Tax Amount", value: formatCurrency(result.tax), highlight: true }
                ]);
                result.rate = "5%";
                break;
            case 'personal':
                result = calculatePersonalIncomeTax(income);
                break;
        }

        displayResults(income, result);
    }

    function calculatePAYE(income) {
        let tax = 0;
        let breakdown = [];
        let rateInfo = "";

        breakdown.push({ label: "Taxable Income", value: formatCurrency(income) });

        if (income <= 24000) {
            tax = income * 0.10;
            breakdown.push(
                { label: "First Band (≤24,000)", value: "10%" },
                { label: "Tax Calculation", value: `10% × ${formatCurrency(income)}` }
            );
            rateInfo = "10%";
        } else if (income <= 32333) {
            tax = (24000 * 0.10) + ((income - 24000) * 0.25);
            breakdown.push(
                { label: "First Band (≤24,000)", value: "10%" },
                { label: "Second Band (24,001-32,333)", value: "25%" },
                { label: "Tax Calculation", value: `(10% × 24,000) + (25% × ${formatCurrency(income - 24000)})` }
            );
            rateInfo = "10-25%";
        } else {
            tax = (24000 * 0.10) + (8333 * 0.25) + ((income - 32333) * 0.30);
            breakdown.push(
                { label: "First Band (≤24,000)", value: "10%" },
                { label: "Second Band (24,001-32,333)", value: "25%" },
                { label: "Third Band (>32,333)", value: "30%" },
                { label: "Tax Calculation", value: `(10% × 24,000) + (25% × 8,333) + (30% × ${formatCurrency(income - 32333)})` }
            );
            rateInfo = "10-30%";
        }

        breakdown.push(
            { label: "Subtotal", value: formatCurrency(tax) },
            { label: "Less: Personal Relief", value: formatCurrency(2400) }
        );

        tax -= 2400;
        const finalTax = tax > 0 ? tax : 0;

        breakdown.push(
            { label: "Final PAYE Tax", value: formatCurrency(finalTax), highlight: true }
        );

        return {
            tax: finalTax,
            breakdown: generateBreakdown(breakdown),
            rate: rateInfo,
            typeName: "PAYE"
        };
    }

    function calculatePersonalIncomeTax(income) {
        let tax = 0;
        let breakdown = [];
        let rateInfo = "";

        breakdown.push({ label: "Taxable Income", value: formatCurrency(income) });

        if (income <= 20000) {
            tax = income * 0.10;
            breakdown.push(
                { label: "First Band (≤20,000)", value: "10%" },
                { label: "Tax Calculation", value: `10% × ${formatCurrency(income)}` }
            );
            rateInfo = "10%";
        } else if (income <= 50000) {
            tax = (20000 * 0.10) + ((income - 20000) * 0.20);
            breakdown.push(
                { label: "First Band (≤20,000)", value: "10%" },
                { label: "Second Band (20,001-50,000)", value: "20%" },
                { label: "Tax Calculation", value: `(10% × 20,000) + (20% × ${formatCurrency(income - 20000)})` }
            );
            rateInfo = "10-20%";
        } else {
            tax = (20000 * 0.10) + (30000 * 0.20) + ((income - 50000) * 0.30);
            breakdown.push(
                { label: "First Band (≤20,000)", value: "10%" },
                { label: "Second Band (20,001-50,000)", value: "20%" },
                { label: "Third Band (>50,000)", value: "30%" },
                { label: "Tax Calculation", value: `(10% × 20,000) + (20% × 30,000) + (30% × ${formatCurrency(income - 50000)})` }
            );
            rateInfo = "10-30%";
        }

        breakdown.push(
            { label: "Final Personal Income Tax", value: formatCurrency(tax), highlight: true }
        );

        return {
            tax,
            breakdown: generateBreakdown(breakdown),
            rate: rateInfo,
            typeName: "PERSONAL INCOME TAX"
        };
    }

    function displayResults(income, result) {
        resultSection.show();
        taxOutput.text(`Estimated ${result.typeName} Tax: ${formatCurrency(result.tax)}`);
        resultType.text(result.typeName);
        resultIncome.text(formatCurrency(income));
        resultRate.text(result.rate);
        breakdownElement.html(result.breakdown);

        $('html, body').animate({
            scrollTop: resultSection.offset().top - 20
        }, 500);
    }

    function showError(message) {
        taxOutput.text(message).css('color', 'red');
        breakdownElement.empty();
        resultSection.hide();
    }

    function formatCurrency(amount) {
        return 'KES ' + amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    function generateBreakdown(items) {
        let html = '<table class="breakdown-table">';
        items.forEach(item => {
            const rowClass = item.highlight ? ' class="highlight-row"' : '';
            html += `<tr${rowClass}>
                <td>${item.label}</td>
                <td>${item.value}</td>
            </tr>`;
        });
        html += '</table>';
        return html;
    }
});
</script>

</body>
</html>