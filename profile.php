<?php
// Start the session
session_start();

// Include the connection file
require_once('connection.php');

// Check if the user is logged in
if(isset($_SESSION['user_email'])) {
    // Retrieve user's email from session
    $user_email = $_SESSION['user_email'];

    // Retrieve user's sales data from the database
    $sql = "SELECT * FROM sales WHERE CustomerId = '$user_email'";
    $result = mysqli_query($con, $sql);

    // Check if any data is returned
    if (mysqli_num_rows($result) > 0) {
        // Output the sales data in a table format
        echo "<!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Receipt</title>
                <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    background-color: #f2f2f2;
                }
            
                .container {
                    max-width: 800px;
                    margin: 20px auto;
                    padding: 20px;
                    background-color: #fff;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    overflow-x: auto; /* Add this line to enable horizontal scrolling */
                }
            
                h1 {
                    text-align: center;
                    margin-bottom: 20px;
                }
            
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
            
                th, td {
                    padding: 10px;
                    text-align: left;
                    border-bottom: 1px solid #ddd;
                    max-width: 200px; /* Set maximum width for table cells */
                    word-wrap: break-word; /* Allow text wrapping */
                }
            
                th {
                    background-color: #f2f2f2;
                    font-weight: bold;
                }
            
                tr:hover {
                    background-color: #f9f9f9;
                }
            
                .print-btn, .back-btn {
                    display: inline-block; /* Display as inline-block to place them next to each other */
                    width: 100px;
                    margin: 20px 10px; /* Adjust margin for spacing */
                    padding: 10px;
                    background-color: #4CAF50;
                    color: #fff;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 16px;
                    text-align: center;
                    text-decoration: none;
                }
                
                .print-btn:hover, .back-btn:hover {
                    background-color: #45a049;
                }
                .print-btn {margin-left:35%;}
                .back-btn {width:auto;}
                
            </style>
            

            </head>
            <body>
                <div class='container'>
                    <h1>Receipt</h1>
                    <table border='1'>
                        <tr><th>Month</th><th>Year</th><th>Item Sold</th><th>Total Amount</th><th>Payment Type</th><th>Payment ID</th></tr>";
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . date("F", strtotime(($row["MonthOfSale"]) . "/1/" . $row["YearOfSale"])) . "</td>"; // Convert month number to month name
                            echo "<td>" . $row["YearOfSale"] . "</td>";
                            echo "<td>" . $row["ItemSold"] . "</td>";
                            echo "<td>" . $row["TotalAmount"] . "</td>";
                            echo "<td>" . $row["PaymentType"] . "</td>";
                            echo "<td>" . $row["PaymentId"] . "</td>";
                            echo "</tr>";
                        }
                        
        echo "</table>";

              // Add a print button
              echo "<button class='print-btn' onclick='window.print()'>Print</button>";

              // Add a button to go back to the home page
              echo "<a href='grocery.php' class='back-btn'>Back to Home</a>";
              
              echo "</div>
                  </body>
                  </html>";
      
    } else {
        echo "No sales data found for the user.";
    }
} else {
    // Redirect the user to the auth page if not logged in
    header("Location: auth.php");
    exit();
}
?>
