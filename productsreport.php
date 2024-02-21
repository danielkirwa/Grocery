<?php
require_once('connection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Report</title>
    <style>
        /* Table styles */
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        /* Image styles */
        img {
            max-width: 80px;
            max-height: 80px;
            border-radius: 5px;
        }

        /* Container styles */
        .container {
            width: 80%;
            margin: 0 auto;
        }

        /* Heading styles */
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Message styles */
        .no-products {
            text-align: center;
            color: #666;
        }

        /* Button styles */
        .print-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .print-button:hover {
            background-color: #0056b3;
        }
        .nav-links {
        display: flex;
        justify-content: space-around;
        background-color: #333;
        padding: 10px;
    }

    .nav-links a {
        color: #fff;
        text-decoration: none;
        padding: 5px 10px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .nav-links a:hover {
        background-color: #555;
    }

    </style>
</head>
<body>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="admin.php">Add Products</a>
            <a href="products.php">View Products</a>
            <a href="user.php">System Users</a>
            <a href="customer.php">Customer Feedback</a>
        </div>
 
    <div class="container">
        <h1>Products Report</h1>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Image</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once('connection.php');

                $sql = "SELECT * FROM product";
                $result = mysqli_query($con, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row["productname"] . "</td>";
                        echo "<td>" . $row["productquantity"] . "</td>";
                        echo "<td><img src='" . $row["file"] . "' alt='" . $row["productname"] . "'></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' class='no-products'>No products available</td></tr>";
                }

                mysqli_close($con);
                ?>
            </tbody>
        </table>

        <button class="print-button" onclick="window.print()">Print Report</button>
    </div>
</body>
</html>

