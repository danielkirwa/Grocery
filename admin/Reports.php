<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        .report-dropdown {
            text-align: center;
        }
        .report-dropdown select {
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
        }
   
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
       
        
    </style>
</head>

<body>
<div class="navbar">
        <h1>Tumaini Groceries</h1>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="admin.php">Add Products</a>
            <a href="products.php">View Products</a>
             <a href="Reports.php">My reports</a>
            <a href="user.php">System Users</a>
            <a href="../logout.php">Log out</a>
        </div>
    </div>
    <h1>My Reports</h1>
    <div class="report-dropdown">
        <select onchange="window.location.href=this.value;">
            <option value="" selected disabled>Select a report</option>
            <option value="salesreport.php">Sales Report</option>
            <option value="customer.php">Customer feedback Report</option>
            <option value="userreport.php">User Report</option>
            <option value="productsavailable.php">productsavailable</option>
            <!-- Add more report options here -->
            <!-- Example: <option value="another_report.php">Another Report</option> -->
        </select>
    </div>
</body>
</html>
