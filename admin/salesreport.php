<!-- salesreport.php -->

<?php
require_once('../connection.php');

// Check session and user privileges (if necessary)

// Fetch data from the sales table
$sql = "SELECT * FROM sales";
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
    </head>
    <body>
        <h1>Sales Report</h1>
        <table>
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
                    echo "<td>{$row['MonthOfSale']}</td>";
                    echo "<td>{$row['YearOfSale']}</td>";
                    echo "<td>{$row['ItemSold']}</td>";
                    echo "<td>{$row['CustomerId']}</td>";
                    echo "<td>{$row['TotalAmount']}</td>";
                    echo "<td>{$row['PaymentType']}</td>";
                    echo "<td>{$row['PaymentId']}</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </body>
    </html>
    <?php
} else {
    echo "No sales records found.";
}

// Close database connection
mysqli_close($con);
?>
