<?php
// Include connection.php and start session if not already started
require_once('../connection.php');
session_start();

// Fetch product name and quantity from the database
$sql = "SELECT productname, productquantity FROM product";
$search_condition = '';

// Check if search parameters are provided
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($con, $_GET['search']);
    $search_condition = " WHERE productname LIKE '%$search%'";
    $sql .= $search_condition;
}

$result = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Report</title>
    <style>
       body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
            button#downloadPDF {
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #008CBA;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        outline: none;
        transition: background-color 0.3s ease;
    }

    button#downloadPDF:hover {
        background-color: #005f6b;
    }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-top: 0;
            color: #333;
        }

        form {
            margin-bottom: 20px;
            display: flex;
        }

        input[type="text"] {
            width: 70%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px 0 0 5px;
            outline: none;
        }

        button[type="submit"] {
            width: 30%;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            outline: none;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 1px solid #ddd;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        
        tr:hover {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Product Report</h2>
        <div class="back">
    <a href="reports.php">
        <button style="padding: 10px 20px; margin-bottom:10px;background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; outline: none;">Back</button>
    </a>
</div>


        <!-- Search form -->
        <form method="GET">
            <input type="text" name="search" placeholder="Search by Product Name...">
            <button type="submit">Search</button>
        </form>

        <!-- Product data table -->
        <table id="productTable">
        <th style="text-align: center;">Products Available</th>
            <tr>
                <th>Product Name</th>
                <th>Product Quantity</th>
            </tr>
            <?php
            // Display product data
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>{$row['productname']}</td>";
                echo "<td>{$row['productquantity']}</td>";
                echo "</tr>";
            }
            ?>
        </table>

        <!-- Download PDF button -->
        <button id="downloadPDF"  onclick="generatePDF()">Download PDF</button>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.0/html2pdf.bundle.min.js"></script>
        <script>
            function generatePDF() {
                const content = document.getElementById('productTable');
                const options = {
                    filename: 'product_report.pdf',
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2 },
                    jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
                };

                html2pdf().from(content).set(options).save();
            }
        </script>
    </div>
</body>
</html>
