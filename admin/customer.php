<?php

 
require_once('../connection.php');

// check seesion
session_start();

// Check if the user is logged in
if(!isset($_SESSION['user_email'])) {
    // If not logged in, redirect to the login page
    header("Location: ../auth.php");
    exit();
}
if($_SESSION['priviledge'] !== 'admin') {
    // If not authorized, redirect to the login page
    header("Location: ../auth.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $id = $_POST['feedbackId'];
    $action = $_POST['response'];
    $dateReplied = date("Y-m-d H:i:s"); // Get current date and time
    $status = 0;

    // Update database with the provided data
    $sqlUpdate = "UPDATE enquiries SET ACTION = '$action', DATEREPLIED = '$dateReplied', STATUS = '$status' WHERE COUNT = $id";

    if (mysqli_query($con, $sqlUpdate)) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/adminreply.css">
    <style>
    .header {
        background-color: #007bff;
        color: #fff;
        padding: 10px 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .header .container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;
    }

    .header .logo {
        font-size: 24px;
        font-weight: bold;
        text-decoration: none;
    }

    .header .nav-links {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
    }

    .header .nav-links li {
        margin-right: 20px;
    }

    .header .nav-links li:last-child {
        margin-right: 0;
    }

    .header .nav-links a {
        color: #fff;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .header .nav-links a:hover {
        color: #ccc;
    }
    .print-button {
    background-color: #4CAF50; /* Green */
    border: solid-black;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin-top: 20px; /* Adjust the top margin as needed */
    margin-left:40%;
    cursor: pointer;
    border-radius: 8px;
}

.print-button:hover {
    background-color: orange; /*orange */
}

/* Center the button horizontally */
body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
        }
        .nav-links {
            margin-top: 10px;
        }
        .nav-links a {
            color: #fff;
            text-decoration: none;
            margin: 0 10px;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin-top: 20px;
        }
        .card {
            width: 300px;
            margin: 20px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .chart-container {
            width: 600px;
            margin-top: 20px;
        }

</style>
</head>
<body>
<div class="navbar">
        <h1><?php echo $_SESSION['user_email']; ?></h1>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="admin.php">Add Products</a>
            <a href="products.php">View Products</a>
            <a href="user.php">System Users</a>
            <a href="customer.php">Customer Feedback</a>
        </div>
    </div>

<?php
    // Display all data from the enquiries table
    $sqlSelectEnquiries = "SELECT NAME, EMAIL, MESSAGE, DATECREATED, STATUS, COUNT FROM enquiries WHERE STATUS = 1";
    $result = mysqli_query($con, $sqlSelectEnquiries);

    $count = 1; // Initialize count variable

    if (mysqli_num_rows($result) > 0) {
        echo "<h2>Enquiries</h2>";
        echo "<table border='1'>";
        echo "<tr><th>Count</th><th>ID</th><th>Name</th><th>Email</th><th>Message</th><th>Date Created</th><th>Status</th><th>Action</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $count . "</td>"; // Display count
            echo "<td>" . $row['COUNT'] . "</td>";
            echo "<td>" . $row['NAME'] . "</td>";
            echo "<td>" . $row['EMAIL'] . "</td>";
            echo "<td>" . $row['MESSAGE'] . "</td>";
            echo "<td>" . $row['DATECREATED'] . "</td>";
            echo "<td>" . $row['STATUS'] . "</td>";
            echo "<td><a href='#' class='respond-link' onclick=\"openForm('{$row['EMAIL']}')\">Respond</a></td>"; // Pass count and email as parameters
            echo "</tr>";
            $count++; // Increment count for next iteration
        }
        echo "</table>";
    } else {
        echo "No enquiries found.";
    }
?>
<div>      <button class="print-button" onclick="window.print()">Print enquiries</button></div>

<!-- Added this div for the pop-up form to reply-->
<div id="respondForm" class="popup-form">
    <form id="responseForm" class="popup-content" method="post" action="customer.php">
        <span class="close" onclick="closeForm()">&times;</span>
        <h2>Respond to Feedback</h2>
        <input type="hidden" id="feedbackId" name="feedbackId">
        <input type="text" id="feedbackName" name="feedbackName" readonly>
        <input type="email" id="feedbackEmail" name="feedbackEmail" readonly>
        <textarea id="feedbackMessage" name="feedbackMessage" readonly></textarea>
        <textarea id="responseMessage" placeholder="Your response..." name="response" required></textarea>
        <button type="submit">Send</button>
    </form>
</div>

<script>
    // Added this JavaScript for showing and hiding the pop-up form
function openForm() {
    document.getElementById("respondForm").style.display = "block";
}

function closeForm() {
    document.getElementById("respondForm").style.display = "none";
}

// Add an event listener to handle opening the form when "Respond" link is clicked
document.querySelectorAll('.respond-link').forEach(item => {
    item.addEventListener('click', event => {
        var row = event.target.closest('tr');
        var id = row.cells[1].innerText;
        var name = row.cells[2].innerText;
        var email = row.cells[3].innerText;
        var message = row.cells[4].innerText;
        var dateCreated = row.cells[5].innerText;

        document.getElementById('feedbackId').value = id;
        document.getElementById('feedbackName').value = name;
        document.getElementById('feedbackEmail').value = email;
        document.getElementById('feedbackMessage').value = message;
        document.getElementById('responseSubject').value = ''; // Clear the subject field
        document.getElementById('responseMessage').value = ''; // Clear the response field

        openForm();
    });
});

</script>
</body>
</html>
