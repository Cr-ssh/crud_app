$(document).ready(function () {
    console.log("Document ready");
    console.log("Current URL params:", window.location.search);

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('income')) {
        console.log("Found income in URL params");
        calculateTaxFromParams(urlParams);
    }

    $('#tax-form').on('submit', function (e) {
        e.preventDefault();
        console.log("Form submitted");

        const income = parseFloat($('#income').val().replace(/[^0-9.]/g, ''));
        const taxType = $('#tax-type').val();

        if (isNaN(income)) {
            alert('Please enter a valid income amount');
            return;
        }

        calculateAndDisplayTax(income, taxType);
    });

    $('#save-btn').on('click', function () {
        const name = $('#calc-name').val().trim();
        const taxType = $('#tax-type').val();
        const income = parseFloat($('#income').val().replace(/[^0-9.]/g, ''));
        const taxAmount = parseFloat($('#tax-output').text().replace(/[^0-9.]/g, ''));
        const recordId = $('#record-id').val(); // hidden field

        if (!name || isNaN(income) || isNaN(taxAmount)) {
            alert('Please enter a valid name and income.');
            return;
        }

        $.ajax({
            url: 'php/save_tax.php',
            method: 'POST',
            dataType: 'json', // Ensure the response is parsed as JSON
            data: {
                record_id: recordId,
                name: name,
                tax_type: taxType,
                income: income,
                tax_amount: taxAmount
            },
            success: function (response) {
                console.log("AJAX response:", response);
                if (response.success) {
                    alert(response.message);
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", xhr.responseText);
                alert('Failed to save the calculation.');
            }
        });
    });
});

function calculateTaxFromParams(params) {
    const income = parseFloat(params.get('income'));
    const taxType = params.get('tax-type') || 'paye';

    if (!isNaN(income)) {
        $('#income').val(income);
        $('#tax-type').val(taxType);
        calculateAndDisplayTax(income, taxType);
    }
}

function calculateAndDisplayTax(income, taxType) {
    console.log(`Calculating tax for ${income} (${taxType})`);

    let taxRate = 0.1;
    let taxAmount = 0;
    let bands = [];

    if (taxType === 'paye') {
        const thresholds = [
            { limit: 24000, rate: 0.1 },
            { limit: 32333, rate: 0.25 },
            { limit: Infinity, rate: 0.3 }
        ];

        let remaining = income;
        let lowerLimit = 0;

        thresholds.forEach(threshold => {
            if (remaining <= 0) return;

            const bandLimit = Math.min(threshold.limit - lowerLimit, remaining);
            const bandTax = bandLimit * threshold.rate;

            bands.push({
                label: `${lowerLimit + 1} - ${threshold.limit === Infinity ? '∞' : threshold.limit}`,
                rate: threshold.rate * 100,
                amount: bandLimit,
                tax: bandTax
            });

            taxAmount += bandTax;
            remaining -= bandLimit;
            lowerLimit = threshold.limit;
        });

        taxRate = taxAmount / income;
    } else if (taxType === 'withholding') {
        taxRate = 0.05;
        taxAmount = income * taxRate;
        bands = [{
            label: 'Withholding Tax',
            rate: 5,
            amount: income,
            tax: taxAmount
        }];
    } else if (taxType === 'personal') {
        taxRate = 0.1;
        taxAmount = income * taxRate;
        bands = [{
            label: 'Personal Income Tax',
            rate: 10,
            amount: income,
            tax: taxAmount
        }];
    }

    // Display Results
    $('#tax-output').text(`Estimated Tax: KES ${taxAmount.toFixed(2)}`);
    $('#result-type').text(taxType.toUpperCase());
    $('#result-income').text(`KES ${income.toLocaleString()}`);
    $('#result-rate').text(`${(taxRate * 100).toFixed(2)}%`);
    $('#results').show();

    // Display Breakdown
    populateTaxBreakdown(bands);

    // Scroll to result
    $('html, body').animate({
        scrollTop: $('#results').offset().top
    }, 500);
}

function populateTaxBreakdown(bands) {
    if (!bands || bands.length === 0) {
        $('#tax-breakdown').hide();
        return;
    }

    let html = `<h5><i class="fas fa-list-alt"></i> Breakdown</h5>`;
    html += `<table class="breakdown-table">
        <thead>
            <tr>
                <th>Band</th>
                <th>Rate</th>
                <th>Taxable Amount</th>
                <th>Tax Due</th>
            </tr>
        </thead>
        <tbody>`;

    bands.forEach(band => {
        html += `<tr>
            <td>${band.label}</td>
            <td>${band.rate}%</td>
            <td>KES ${band.amount.toLocaleString()}</td>
            <td>KES ${band.tax.toLocaleString()}</td>
        </tr>`;
    });

    html += `</tbody></table>`;
    $('#tax-breakdown').html(html).fadeIn();
}
