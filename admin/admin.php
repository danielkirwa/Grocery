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

?>

<?php

if(isset($_POST['addproduct'])) {
    // Retrieve form data
    $productcode = $_POST['productcode'];
    $productname = $_POST['productname'];
    $productprice = $_POST['productprice'];
    $productqty = $_POST['productqty'];
    $productdescription = $_POST['productdescription'];
    $productstatus = isset($_POST['productstatus']) ? $_POST['productstatus'] : 0; // Default status to 0 if not provided

    // File upload handling
    $file = $_FILES['productimage'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileError = $file['error'];

    // Destination folder
    $destinationFolder = '../assets/';

    // Check if file uploaded successfully
    if ($fileError === 0) {
        // Check if the file is already in the assets folder
        if (strpos($fileTmpName, $destinationFolder) === false) {
            // Move the uploaded file to the assets folder
            $fileDestination = $destinationFolder . $fileName;
            move_uploaded_file($fileTmpName, $fileDestination);
        } else {
            $fileDestination = $fileTmpName;
        }

        // Insert data into the database
        $sql = "INSERT INTO product (productcode, productname, productquantity, productdescription, productstatus, productprice, file)
                VALUES ('$productcode', '$productname', '$productqty', '$productdescription', '$productstatus', '$productprice', '$fileDestination')";
        
        if(mysqli_query($con, $sql)) {
            echo "Product added successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
    } else {
        echo "There was an error uploading your file.";
    }
}

if(isset($_POST['removeproduct'])) {
    // Retrieve product code to remove
    $productCodeToRemove = $_POST['productcode'];
    
    // Remove product from the database
    $sql = "DELETE FROM product WHERE productcode='$productCodeToRemove'";
    
    if(mysqli_query($con, $sql)) {
        echo "Product removed successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/admin.css">
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
            <a href="user.php">System Users</a>
            <a href="customer.php">Customer Feedback</a>
        </div>
    </div>

<div class="container">
    <form action="" method="post" class="addproduct" enctype="multipart/form-data">
        <h3>Add a New Product</h3>
        <input type="number" name="productcode" placeholder="Enter Product Code" class="box" required>
        <input type="text" name="productname" placeholder="Enter Product Name" class="box" >
        <input type="number" name="productprice" placeholder="Enter Product Price" class="box" >
        <input type="number" name="productqty" placeholder="Enter Product Quantity" class="box" >
        <input type="text" name="productdescription" placeholder="Enter Product Description" class="box" >
        <input type="file" name="productimage" accept="image/jpg,image/png,image/jpeg" class="box" >
        <input type="submit" value="Add the Product" name="addproduct" class="btn">
        <input type="submit" value="Remove Product" name="removeproduct" class="btn">
    </form>
</div>

</body>
</html>
