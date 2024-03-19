<?php
require_once('../connection.php');
?>
<?php
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



// Your PHP code for database connection and fetching data comes here
// Fetch data from database
$sql = "SELECT COUNT(*) AS total_users FROM register";
$result = $con->query($sql);

// Initialize total users variable
$totalUsers = 0;

// Check if query executed successfully
if ($result) {
    $row = $result->fetch_assoc();
    $totalUsers = $row['total_users'];
}
?>
<?php
// Your PHP code for database connection and fetching data comes here
// Fetch data from database
$sql = "SELECT DATE(datecreated) AS created_date, COUNT(*) AS user_count FROM register GROUP BY DATE(datecreated)";
$result = $con->query($sql);

// Initialize arrays to store labels and data
$labels = [];
$data = [];

// Loop through the results and populate the arrays
while ($row = $result->fetch_assoc()) {
    $labels[] = $row['created_date'];
    $data[] = $row['user_count'];
}

// Convert arrays to JSON format
$labelsJSON = json_encode($labels);
$dataJSON = json_encode($data);
?>
<?php
$sql = "SELECT MonthOfSale, SUM(TotalAmount) AS TotalAmount FROM sales GROUP BY MonthOfSale";
$result = mysqli_query($con, $sql);

// Step 2: Process the fetched data
$months = [];
$totalAmounts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $months[] = $row['MonthOfSale'];
    $totalAmounts[] = $row['TotalAmount'];
}

?>
<?php
require_once('../connection.php');

$productNames = [];
$productQuantities = [];

$sql = "SELECT productname, productquantity FROM product";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $productNames[] = $row["productname"];
        $productQuantities[] = $row["productquantity"];
    }
}

mysqli_close($con);
?>

<?php
    // Calculate total sales by summing up all values in the $totalAmounts array
    $totalSales = 0;
    foreach ($totalAmounts as $amount) {
        $totalSales += $amount;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
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

    <div class="container">
        <!-- Sales Card -->
        <div class="card" onclick="window.location.href='salesreport.php';">
            <h2>Total number of sales</h2>
            <p>Total sales: <?php echo $totalSales; ?></p>
            <canvas id="salesChart" width="400" height="200"></canvas>
        </div>

        <!-- Users Card -->
        <div class="card" onclick="window.location.href='userreport.php';">
            <h2>total number of users</h2>
            <p>Total users: <?php echo $totalUsers; ?></p>
            <canvas id="usersChart"></canvas>
        </div>
        
        <!-- Products Card -->
        <div class="card" onclick="window.location.href='productsavailable.php';">
            <h2>Total products</h2>
            <canvas id="productQuantityChart"></canvas>
            <p>Total products: <?php echo array_sum($productQuantities); ?></p>
        </div>
    </div>

    <script>
        var ctx = document.getElementById('salesChart').getContext('2d');
        var salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Total Amount',
                    data: <?php echo json_encode($totalAmounts); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    </script>

    <script>
        // Sample users data (replace with actual data fetched from PHP)
        const usersData = {
            labels: <?php echo $labelsJSON; ?>,
            datasets: [{
                label: 'Users',
                data: <?php echo $dataJSON; ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        };

        // Render users chart as a bar chart
        const usersChartCanvas = document.getElementById('usersChart').getContext('2d');
        const usersChart = new Chart(usersChartCanvas, {
            type: 'bar',
            data: usersData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    <script>
        // Product data fetched from PHP
        const productNames = <?php echo json_encode($productNames); ?>;
        const productQuantities = <?php echo json_encode($productQuantities); ?>;
        
        // Generate random colors for each product
        const generateRandomColor = () => {
            return '#' + Math.floor(Math.random()*16777215).toString(16);
        };
        const backgroundColors = productNames.map(() => generateRandomColor());

        // Render bar chart
        const productQuantityChartCanvas = document.getElementById('productQuantityChart').getContext('2d');
        const productQuantityChart = new Chart(productQuantityChartCanvas, {
            type: 'bar',
            data: {
                labels: productNames,
                datasets: [{
                    label: 'Product Quantity',
                    data: productQuantities,
                    backgroundColor: backgroundColors,
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    </script>
</body>
</html>

