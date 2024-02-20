<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('connection.php');

// Check if form is submitted to update user details
if (isset($_POST['save'])) {
    // Loop through the submitted data to update user details
    foreach ($_POST['fullname'] as $key => $fullname) {
        
        // Check if the other arrays are set as well
        if (isset($_POST['email'][$key], $_POST['phonenumber'][$key], $_POST['idnumber'][$key])) {
            $email = $_POST['email'][$key];
            $phonenumber = $_POST['phonenumber'][$key];
            $idnumber = $_POST['idnumber'][$key];

            // Update user details in the register table using the ID number as the identifier
            $sqlUpdateRegister = "UPDATE register SET fullname='$fullname', email='$email', phonenumber='$phonenumber' WHERE idnumber='$idnumber'";
            if ($con->query($sqlUpdateRegister) !== true) {
                echo "Error updating user: " . mysqli_error($con);
            }

            // Update user details in the user table using the email as the identifier
            $sqlUpdateUser = "UPDATE user SET username='$email' WHERE username='$email'";
            if ($con->query($sqlUpdateUser) !== true) {
                echo "Error updating user: " . mysqli_error($con);
            }
        }
    }
    // Reload the page after updating
    echo "<script>window.location.href = window.location.href;</script>";
}


// Check if add user button is clicked//trying to add the infor entered into the dtabase
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['adduser'])) {
    // Validate form data
    $new_fullname = $_POST['new_fullname'];
    $new_email = $_POST['new_email'];
    $new_phonenumber = $_POST['new_phonenumber'];
    $new_idnumber = $_POST['new_idnumber'];
    $datecreated = date("Y-m-d H:i:s");

    // Generate MD5 hashed password
    $default_password = md5($new_idnumber);

    // Start a transaction
    $con->begin_transaction();

    // Insert new user details into the register table
    $sqlInsertRegister = "INSERT INTO register (fullname, email, phonenumber, datecreated, idnumber) 
                     VALUES ('$new_fullname', '$new_email', '$new_phonenumber', '$datecreated', '$new_idnumber')";

    // Insert into user table with default password
    $sqlInsertUser = "INSERT INTO user (username, userpassword, priviledge) 
                     VALUES ('$new_email', '$default_password', 'user')";

    // Execute both queries
    if ($con->query($sqlInsertRegister) === TRUE && $con->query($sqlInsertUser) === TRUE) {
        // Commit the transaction if both queries are successful
        $con->commit();
        echo "New record created successfully";
    } else {
        // Rollback the transaction if any query fails
        $con->rollback();
        echo "Error: " . $con->error;
    }

    // Reload the page after adding the user
    echo "<script>window.location.href = window.location.href;</script>";
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the delete button is clicked
    if (isset($_POST['delete'])) {
        // Check if any user is selected for deletion
        if (isset($_POST['selectedUsers'])) {
            // Start a transaction
            $con->begin_transaction();

            // Loop through each selected user and delete them from both tables
            foreach ($_POST['selectedUsers'] as $userId) {
                // Delete from register table
                $sqlDeleteRegister = "DELETE FROM register WHERE idnumber='$userId'";
                $resultRegister = $con->query($sqlDeleteRegister);

                // Delete from user table
             //   $sqlDeleteUser = "DELETE FROM user WHERE username=' $email'";
               // $resultUser = $con->query($sqlDeleteUser);
               // Get the email associated with the current user ID
    $sqlGetEmail = "SELECT email FROM register WHERE idnumber='$userId'";
    $resultEmail = $con->query($sqlGetEmail);
    if ($resultEmail->num_rows > 0) {
        $row = $resultEmail->fetch_assoc();
        $email = $row['email'];
    } else {
        echo "Error: User not found";
        exit;
    }

    // Delete from user table using the retrieved email
    $sqlDeleteUser = "DELETE FROM user WHERE username='$email'";
    $resultUser = $con->query($sqlDeleteUser);

                // Check if both deletions were successful
                if ($resultRegister === FALSE || $resultUser === FALSE) {
                    // Rollback the transaction if any deletion fails
                    $con->rollback();
                    echo "Error deleting user with ID: $userId";
                    exit;
                }
            }

            // Commit the transaction if all deletions are successful
            $con->commit();
            
            // Redirect to the same page after deletion
            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        } else {
            echo "Please select at least one user to delete.";
        }
    }
}

// Fetch user records from the database
$sqlSelectUsers = "SELECT * FROM register";
$result = mysqli_query($con, $sqlSelectUsers);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    
    <style>
         table {
        width: 100%;
        border-collapse: collapse;
    }
    
    th, td {
        
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
        
    }
    
    th {
        background-color: #f2f2f2;
        background:orange;
    }
    
    tr:hover {
        background-color: #f2f2f2;
    }
    
    input[type="text"],
    input[type="email"] {
        width: 100%;
        padding: 5px;
        box-sizing: border-box;
    }
    
    button {
        background-color: #4CAF50;
        color: white;
        padding: 8px 20px;
        border: none;
        cursor: pointer;
        border-radius: 4px;
    }
    
    button:hover {
        background-color: #45a049;
    }
    .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            animation-name: modalopen;
            animation-duration: 0.4s;
        }

        @keyframes modalopen {
            from {opacity: 0}
            to {opacity: 1}
        }

        /* Close button style */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
      
      <link rel="stylesheet" href="css/general.css">
   
    <script>
       

        function confirmDeleteUser() {
            // JavaScript confirmation for deleting a user
            var confirmation = confirm("Are you sure you want to delete selected user(s)?");
            if (confirmation) {
                // Proceed with form submission if user confirms
                document.getElementById('deleteForm').submit();
            }
        }
        function printReport() {
        window.print();
    }
    </script>
</head>
<body>
<header class="">
    <div class="usernav">
        <nav class="">
            <a href="dashboard.php">Dashboard</a>
            <a href="admin.php">Add Products</a>
            <a href="products.php">View Products</a>
            <a href="user.php">System Users</a>
            <a href="customer.php">Customer Feedback</a>
        </nav>
    </div>
</header>

<h2>User Management</h2>

<form method="POST">
    <table>
        <tr>
            <th>Select</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>ID Number</th>
        </tr>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td><input type='checkbox' name='selectedUsers[]' value='{$row['idnumber']}'></td>";
            echo "<td><input type='text' name='fullname[]' value='{$row['fullname']}'></td>";
            echo "<td><input type='email' name='email[]' value='{$row['email']}'></td>";
            echo "<td><input type='text' name='phonenumber[]' value='{$row['phonenumber']}'></td>";
            echo "<td><input type='text' name='idnumber[]' value='{$row['idnumber']}'></td>";
            echo "</tr>";
        }
        ?>
        <tr id="newRow" style="display: none;">
            <td><input type='checkbox' name='selectedUsers[]'></td>
            <td><input type='text' name='fullname[]'></td>
            <td><input type='email' name='email[]'></td>
            <td><input type='text' name='phonenumber[]'></td>
            <td><input type='text' name='idnumber[]'></td>
        </tr>
        <tr>
            <td colspan="5">
                <button type="button"name="adduserrow" onclick="addUserRow()">Add Userrow</button>
                <!-- added an add user button but now working still -->
                <button type="submit" name="delete">Delete User</button>
                <button type="submit" name="save">Save Changes</button>
                <button type="button" onclick="printReport()">Print Report</button>
            </td>
        </tr>
    </table>
</form>

<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="deleteUser">
</form>
<!-- popup  register -->
<!-- Modal for adding a new userrow -->
<div id="addUserModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Add User</h2>
        <form method="post">
            <label for="new_fullname">Enter Name:</label>
            <input type="text" id="new_fullname" name="new_fullname" required><br><br>
            
            <label for="new_email">Enter Email:</label>
            <input type="email" id="new_email" name="new_email" required><br><br>
            
            <label for="new_idnumber">Enter ID:</label>
            <input type="text" id="new_idnumber" name="new_idnumber" required><br><br>
            
            <label for="new_phonenumber">Enter Phone:</label>
            <input type="text" id="new_phonenumber" name="new_phonenumber" required><br><br>
            
            <input type="submit" value="Submit" name="adduser">
        </form>
    </div>
</div>

</body>
<script>
    function addUserRow() {
        // Show the modal when "Add Userrow" button is clicked
        document.getElementById('addUserModal').style.display = 'block';
    }

    function closeModal() {
        // Close the modal when close button is clicked
        document.getElementById('addUserModal').style.display = 'none';
    }
</script>
</html>

<?php
//mysqli_close($con);
?>