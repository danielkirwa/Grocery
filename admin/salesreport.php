<?php

require_once('../connection.php');

// Check session and user privileges (if necessary)

// Initialize variables to store search criteria
$searchMonth = "";
$searchCustomerId = "";

// Check if the search form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["searchByMonth"])) {
        $searchMonth = $_POST["month"];
    } elseif (isset($_POST["searchByCustomerId"])) {
        $searchCustomerId = $_POST["customerId"];
    }
}

// Fetch data from the sales table based on search criteria
$sql = "SELECT * FROM sales WHERE 1";

// Add search criteria to the SQL query if provided
if (!empty($searchMonth)) {
    $sql .= " AND MonthOfSale = '$searchMonth'";
}
if (!empty($searchCustomerId)) {
    $sql .= " AND CustomerId = '$searchCustomerId'";
}

$result = mysqli_query($con, $sql);

// Check if there are any sales records
if (mysqli_num_rows($result) > 0) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sales Report</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 1000px;
                margin: 20px auto;
                padding: 20px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                background-color: #fff;
            }
            h1 {
                text-align: center;
                margin-bottom: 20px;
            }
            form {
                margin-bottom: 20px;
                text-align: center;
            }
            form input[type="text"] {
                padding: 8px;
                margin-right: 10px;
                width: 150px;
            }
            form button {
                padding: 8px 20px;
                background-color: #007bff;
                color: #fff;
                border: none;
                cursor: pointer;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            table, th, td {
                border: 1px solid #ccc;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
            }
            .download-button {
                text-align: right;
                margin-bottom: 20px;
            }
            .download-button button {
                padding: 8px 20px;
                background-color: #28a745;
                color: #fff;
                border: none;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Sales Report</h1>
               <!-- Download button -->
               <div class="back">
    <a href="reports.php">
        <button style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; outline: none;">Back</button>
    </a>
</div>


            <!-- Download button -->
            <div class="download-button">
                <button onclick="downloadTable()">Download Report</button>
            </div>

            <!-- Search form -->
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <label for="month">Search by Month:</label>
                <input type="text" id="month" name="month" value="<?php echo htmlspecialchars($searchMonth); ?>" placeholder="Enter month...">
                <button type="submit" name="searchByMonth">Search</button><br><br>
                <label for="customerId">Search by Customer ID:</label>
                <input type="text" id="customerId" name="customerId" value="<?php echo htmlspecialchars($searchCustomerId); ?>" placeholder="Enter customer ID...">
                <button type="submit" name="searchByCustomerId">Search</button>
            </form>
            
            <!-- Sales table -->
            <table id="salesTable">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Year</th>
                        <th>Item Sold</th>
                        <th>Customer ID</th>
                        <th>Total Amount</th>
                        <th>Payment Type</th>
                        <th>Payment ID</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Loop through each row in the result set and display it in the table
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['MonthOfSale']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['YearOfSale']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ItemSold']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['CustomerId']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['TotalAmount']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['PaymentType']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['PaymentId']) . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <script>
    function downloadTable() {
        var tableContent = document.getElementById('salesTable').outerHTML;
        var styles = `
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                table, th, td {
                    border: 1px solid #ccc;
                    padding: 8px;
                    text-align: left;
                }
                th {
                    background-color: #f2f2f2;
                }
            </style>
        `;
        var fullHtml = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Sales Report</title>' + styles + '</head><body>' + tableContent + '</body></html>';
        var blob = new Blob([fullHtml], { type: 'text/html' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'sales_report.html';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
</script>
    </body>
    </html>
    <?php
} else {
    echo "No sales records found.";
}

// Close database connection
mysqli_close($con);
?>