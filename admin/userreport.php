<?php
// Include connection.php and start session if not already started
require_once('../connection.php');
session_start();

// Fetch all records initially
$sqlSelectUsers = "SELECT fullname, email, phonenumber, datecreated, idnumber FROM register";
$search_condition = '';

// Check if search parameters are provided
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($con, $_GET['search']);
    $search_condition = " WHERE fullname LIKE '%$search%' OR email LIKE '%$search%' OR phonenumber LIKE '%$search%' OR datecreated LIKE '%$search%' OR idnumber LIKE '%$search%'";
    $sqlSelectUsers .= $search_condition;
}

$result = mysqli_query($con, $sqlSelectUsers);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Reports</title>
    <style>
       body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
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

        button.download-pdf {
            padding: 10px 20px;
            background-color: #008CBA;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            outline: none;
        }

        button.download-pdf:hover {
            background-color: #005f6b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.0/html2pdf.bundle.min.js"></script>
</head>
<body>
    <h2>User Reports</h2>

    <!-- Search form -->
    <form method="GET">
        <input type="text" name="search" placeholder="Search...">
        <button type="submit">Search</button>
    </form>

    <!-- Download PDF button -->
    <button onclick="generatePDF()">Download PDF</button>

    <!-- User data table -->
    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Date Created</th>
            <th>ID Number</th>
        </tr>
        <?php
        // Display user data
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['fullname']}</td>";
            echo "<td>{$row['email']}</td>";
            echo "<td>{$row['phonenumber']}</td>";
            echo "<td>{$row['datecreated']}</td>";
            echo "<td>{$row['idnumber']}</td>";
            echo "</tr>";
        }
        ?>
    </table>

    <script>
        function generatePDF() {
            const content = document.body;
            const options = {
                filename: 'user_report.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            html2pdf().from(content).set(options).save();
        }
    </script>
</body>
</html>
