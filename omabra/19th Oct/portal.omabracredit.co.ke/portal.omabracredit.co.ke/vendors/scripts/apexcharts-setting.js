// Fetch data for the chart via AJAX when the page loads
document.addEventListener("DOMContentLoaded", function() {
    fetchChartData(); // Fetch the default latest loan data
});

// Handle loan selection from dropdown
document.querySelector("#loanSelector").addEventListener("change", function() {
    var loanId = this.value;
    fetchChartData(loanId); // Fetch data for the selected loan
});

function fetchChartData(loanId = null) {
    // Make an AJAX request to fetch loan data from the server
    fetch("../../fetch-loan-data.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ loan_id: loanId })
    })
    .then(response => response.json())
    .then(data => {
        console.log(data); // Check the response in the console for debugging
        if (data && data.expected_payments_data.length > 0) {
            // If payments_data is empty, create an array of zeros
            if (!data.payments_data.length) {
                data.payments_data = data.expected_payments_data.map(() => ({ total_payments: 0, payment_date: null }));
            }
            updateChart(data);
        } else {
            console.error("No data available for chart.");
        }
    });
}

// Update the chart with new data
function updateChart(data) {
    var paymentsSeries = [];
    var expectedPaymentsSeries = [];
    var principalSeries = [];
    var xCategories = [];

    // Variables to hold cumulative sums
    var cumulativePayments = 0;

    // Iterate over expected payments data
    data.expected_payments_data.forEach((expectedPayment, index) => {
        var expectedDate = expectedPayment.payment_date;
        var expectedTotalPayments = parseFloat(expectedPayment.cumulative_installment_amount) || 0;
        var cumulativePrincipal = parseFloat(expectedPayment.cumulative_principal) || 0; // Principal includes requested_amount + cumulative interest

        // Add cumulative expected payments and cumulative principal
        expectedPaymentsSeries.push(expectedTotalPayments);
        principalSeries.push(cumulativePrincipal); // Principal + interest from PHP

        // Get the corresponding payment data (or use 0 if no payment has been made)
        var payment = data.payments_data[index] || { total_payments: 0 };
        cumulativePayments += parseFloat(payment.total_payments || 0);
        paymentsSeries.push(cumulativePayments); // Push cumulative payments up to this expected date

        // Set the x-axis category as the expected payment date
        xCategories.push(expectedDate);
    });

    // Chart configuration with ApexCharts
    var options3 = {
        series: [{
            name: 'Payments',
            data: paymentsSeries
        }, {
            name: 'Expected Payments',
            data: expectedPaymentsSeries
        }, {
            name: 'Principal + Interest',
            data: principalSeries
        }],
        xaxis: {
            categories: xCategories,
        },
        chart: {
            type: 'line' // Specify the type of chart
        }
    };

    // Destroy the previous chart if it exists
    if (window.chart) {
        window.chart.destroy();
    }

    // Render the chart
    window.chart = new ApexCharts(document.querySelector("#chart3"), options3);
    window.chart.render();
}

// Debugging: Log the series data
//console.log(paymentsSeries);
//console.log(expectedPaymentsSeries);
//console.log(principalSeries);
