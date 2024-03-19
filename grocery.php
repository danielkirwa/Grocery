<?php
require_once('connection.php');
?>
<?php
// check session
session_start();
if(!isset($_SESSION['user_email'])) {
    // If not logged in, redirect to the login page
    header("Location: auth.php");
    exit();
}


 
if(isset($_POST['submit'])) {
    // Retrieve form data
    $NAME = $_POST['NAME'];
    $EMAIL = $_POST['EMAIL'];
    $MESSAGE = $_POST['MESSAGE'];
    if(empty($NAME) || empty($EMAIL) || empty($MESSAGE)) {
        // Display error message and prevent form submission
        echo "Please fill in all fields.";
    } else {
        // Get current date in YYYY-MM-DD format
        $TODAY = date('Y-m-d');
        // Assuming default status is 1
        $STATUS = 1;

        // Perform SQL query to insert form data into the database
        $sqlenquiry = "INSERT INTO enquiries (NAME, EMAIL, MESSAGE, STATUS, DATECREATED) VALUES ('$NAME', '$EMAIL', '$MESSAGE', '$STATUS', '$TODAY')";

        if (mysqli_query($con, $sqlenquiry)) {
            // If query is successful, display success message or perform any other actions
            echo "Record added successfully";
        } else {
            // If there is an error in the query, display error message
            echo "Error: " . $sqlenquiry . "<br>" . mysqli_error($con);
        }

        // Close database connection
        //mysqli_close($con);
    }
}

?>

<?php
// Retrieve data from POST request
if(isset($_POST['cartData'])) {
    // Retrieve data from POST request
    $cartData = json_decode($_POST['cartData'], true);
    $customerId = $_SESSION['user_email'];
    $totalAmount = $_POST['totalAmount'];
    $paymentType = $_POST['paymentType'];
    $paymentId = $_POST['paymentId'];
    $month = $_POST['month'];
    $year = $_POST['year'];


    // Prepare and execute SQL statement to insert data into database
    $cartDataJSON = json_encode($cartData); // Convert cart data to JSON
    $sql = "INSERT INTO sales (MonthOfSale, YearOfSale, ItemSold, CustomerId, TotalAmount, PaymentType, PaymentId) 
            VALUES ('$month', '$year', '$cartDataJSON', '$customerId', '$totalAmount', '$paymentType', '$paymentId')";

    if (mysqli_query($con, $sql)) {
        echo "Data inserted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }
}

// Close connection
//$con->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tumaini Groceries</title>
    <style>
        /* Your CSS styles here */
        .product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .product {
            border: 1px solid #ccc;
            padding: 20px;
            width: 300px;
        }

        .product img {
            max-width: 100%;
            height: auto;
            margin-bottom: 10px;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .product button {
            background-color: #4CAF50;
            border: none;
            color: yellow;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            transition-duration: 0.4s;
        }

        .product button:hover {
            background-color: #45a049;
        }

/text style/
/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 10% auto;
    padding: 20px;
    border: 1px solid #888;
    border-radius: 10px;
    width: 80%;
    max-width: 600px;
    position: relative;
}

/* Close button styles */
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

/* Checkout button styles */
#checkout-btn {
    border: 2px solid black;
    font-size: 16px;
    border-radius: 20px;
    margin: 20px;
    padding: 10px 20px;
    background: orange;
    width: 150px;
    height: 50px;
    color: black;
    cursor: pointer;
}

#checkout-btn:hover {
    background: #ffa500;
}

#cart-summary p {
    font-weight: bold;
}
#payment-icons img {
    width: 120px; /* Adjust the width as needed */
    margin-right: 10px; /* Adjust the spacing between icons */
}
/* CSS styles */
#payment-icons input[type="radio"] {
    display: none; /* Hide the radio buttons */
}

#payment-icons label.payment-method {
    cursor: pointer; /* Change cursor to pointer on hover */
}

#payment-icons label.payment-method img {
    width: 120px; /* Adjust the width as needed */
    margin-right: 10px; /* Adjust the spacing between icons */
    border: 2px solid transparent; /* Add border to icons */
}

#payment-icons label.payment-method:hover img {
    border-color: orange; /* Change border color on hover */
}

#payment-icons input[type="radio"]:checked + label.payment-method img {
    border-color: orange; /* Change border color for checked icon */
}
/* Checkout button style */
.checkout-button {
    border: none;
    font-size: 16px;
    border-radius: 20px;
    margin: 20px;
    padding: 10px 20px;
    width: 150px;
    height: 50px;
    color: white;
    background-color: orange;
    cursor: pointer;
    outline: none; /* Remove focus outline */
    transition: background-color 0.3s ease; /* Smooth transition for background color */
}

.checkout-button:hover {
    background-color: #ff8c00; /* Darker orange on hover */
}
.cart-item button.remove,
.cart-item button.addition {
    background-color: orange;
   border:solid black;
   font-weight:bolder;
    color: red;
    border-radius:20px;
    padding:5px 10px;
    font-size: 32px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.cart-item button.remove:hover,
.cart-item button.addition:hover {
    background-color: #ddd;
}

.cart-item button.remove:active,
.cart-item button.addition:active {
    background-color: #bbb;
}






/*text style*/
/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 10% auto;
    padding: 20px;
    border: 1px solid #888;
    border-radius: 10px;
    width: 80%;
    max-width: 600px;
    position: relative;
}

/* Close button styles */
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

/* Checkout button styles */
#checkout-btn {
    border: 2px solid black;
    font-size: 16px;
    border-radius: 20px;
    margin: 20px;
    padding: 10px 20px;
    background: orange;
    width: 150px;
    height: 50px;
    color: black;
    cursor: pointer;
}

#checkout-btn:hover {
    background: #ffa500;
}

#cart-summary p {
    font-weight: bold;
}
#payment-icons img {
    width: 120px; /* Adjust the width as needed */
    margin-right: 10px; /* Adjust the spacing between icons */
}
/* CSS styles */
#payment-icons input[type="radio"] {
    display: none; /* Hide the radio buttons */
}

#payment-icons label.payment-method {
    cursor: pointer; /* Change cursor to pointer on hover */
}

#payment-icons label.payment-method img {
    width: 120px; /* Adjust the width as needed */
    margin-right: 10px; /* Adjust the spacing between icons */
    border: 2px solid transparent; /* Add border to icons */
}

#payment-icons label.payment-method:hover img {
    border-color: orange; /* Change border color on hover */
}

#payment-icons input[type="radio"]:checked + label.payment-method img {
    border-color: orange; /* Change border color for checked icon */
}
/* Checkout button style */
.checkout-button {
    border: none;
    font-size: 16px;
    border-radius: 20px;
    margin: 20px;
    padding: 10px 20px;
    width: 150px;
    height: 50px;
    color: white;
    background-color: orange;
    cursor: pointer;
    outline: none; /* Remove focus outline */
    transition: background-color 0.3s ease; /* Smooth transition for background color */
}

.checkout-button:hover {
    background-color: #ff8c00; /* Darker orange on hover */
}

.user-menu {
    position: relative;
    display: inline-block;
}

.user-popup-container {
    position: relative;
    background:orange;
}

.user-popup {
    background:orange;
    display: none;
    position: absolute; /* Change position to absolute */
    top: calc(100% + 10px); /* Position below the user-menu */
    left: 0; /* Align with the left edge of the user-menu */
    background-color: orange;
    min-width: 120px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    z-index: 1000; /* Adjust the z-index value as needed */
}

.user-menu:hover .user-popup {
    display: block; /* Show popup on hover */
}

.user-popup a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}
.user-popup a:hover {
    background-color:red; /* Change background color on hover */
}





    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="css/grocery.css">
    <link rel="stylesheet" href="css/general.css">
</head>

<body>
    <header class="header">
        <a href="#" class="logo"><i class="fas fa-shopping-basket"></i> Tumaini</a>
        <nav class="navbar">
            <a href="#Home">Home</a>
            <a href="#About">About</a>
            <a href="#Products">Products</a>
            <a href="#Reviews">Reviews</a>
            <a href="#Contact">Contact</a>
        </nav>
        
        <div class="icons">
            <div class="fas fa-bars" id="menu-btn"></div>
            <div class="fas fa-search" id="search-btn"></div>
            <div class="fas fa-shopping-cart" id="cart-btn"><span id="itemsincart">0</span></div>
            <div class="user-menu">
    <div class="fas fa-user" id="login-btn"></div>
    <div class="user-popup" id="user-popup" style="width:170px; height:120px; background:orange;">
        <a href="profile.php"style="font-size:16px;"><?php echo $_SESSION['user_email']; ?></a>
        <a href="logout.php">Logout</a>
    </div>
        </div>
        <form action="" class="search-form">
            <input type="search" id="search-box" placeholder="search here...">
            <label for="search-box" class="fas fa-search"></label>
        </form>
    </header>
    <br>
    <br>
    <br>
    <br>
    <br>

    <!-- Home Section - Carousel -->
    <section id="Home">
    <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active ">
      <img src="assets/test1.png" class="d-block w-100" alt="...">
    </div>
    <div class="carousel-item">
      <img src="assets/home5.png" class="d-block w-100" alt="...">
    </div>
    <div class="carousel-item">
      <img src="assets/home4.png" class="d-block w-100" alt="...">
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>


    </section>

    <!-- About Section -->
    <section id="About">
        <div class="container">
            <h2>About Tumaini Groceries</h2>
            <p> Welcome to Tumaini Groceries, your premier destination for all your grocery needs! 
                Step into a world of convenience and quality as we bring the supermarket experience directly to your fingertips. 
                From fresh produce to pantry essentials, we offer a wide selection of high-quality products curated to meet your every culinary requirement. 
                With our user-friendly online platform, shopping for groceries has never been easier.
                Say goodbye to long queues and crowded aisles – simply browse, click, and have your items delivered straight to your doorstep.
                Experience the joy of hassle-free shopping with Tumaini Groceries today!</p>
        </div>
    </section>

    <!-- Products Section -->
    <section id="Products" style="padding-left:100px;">
    <h2>Featured Products</h2>
    <div class="product-container">
        <?php
        require_once('connection.php');

        $sql = "SELECT * FROM product";
        $result = mysqli_query($con, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='product'>";
                echo "<h3>" . $row["productname"] . "</h3>";
                echo "<p>Price:Ksh " . $row["productprice"] . "</p>";
                echo "<p>Quantity: <span id='quantity-" . $row["productcode"] . "' class='itemsavailable'>" . $row["productquantity"] . "</span></p>";
                echo "<p>Description: " . $row["productdescription"] . "</p>";
                echo "<img src='" . $row["file"] . "' alt='" . $row["productname"] . "'>";
                echo '<div class="cartcontrols">';
                echo '  <div>';
                echo '      <button class="minus">-</button>';
                echo '      <label class="label">0</label>';
                echo '      <button class="plus">+</button>';
                echo '  </div>';
                echo '  <button class="add" data-product-code="' . $row["productcode"] . '" data-product-name="' . $row["productname"] . '" data-product-price="' . $row["productprice"] . '"><i class="fas fa-shopping-cart"></i> Add</button>';        
                echo '</div>';
                
                echo "</div>"; // Close product container
            }
        } else {
            echo "No products available";
        }

        mysqli_close($con);
        ?>
    </div>
</section>

<div id="cart-modal" class="modal">
<form action="stkkpush.php" method="POST" method="POST"> 
<div class="modal-content">
        <span class="close">&times;</span>
        <h2>My products</h2>
        <div id="cart-items" style="margin: 20px;">
            <!-- Cart items will be displayed here -->
        </div>
        <div id="cart-summary" style="margin: 20px;">
           
            <p>Total: <span id="total">$0.00</span></p>
            <input type="hidden" name="stktotal" id="stktotal" value="">
            <input type="hidden" name="stktotal" id="stktotal" value="">
            <p>Date/Time: <span id="timestamp"></span></p>
           
            <div id="payment-icons">
            <input type="radio" id="mpesa" name="payment-method" value="mpesa">
                <label for="mpesa" class="payment-method"><img src="https://www.safaricom.co.ke/images/MicrosoftTeams-image.jpg" alt="M-Pesa" title="M-Pesa"></label>
              
            </div>
            <label>Confirm the number below or change </label></br>
            <input type="text" id="txtmpesa" name="txtmpesa" value="" placeholder="254712345678">
            <label>Input your phone number</label></br>
            <input type="number" id="txtmpesa" name="txtmpesa" value="" placeholder="254712345678" onkeyup="validateNumber(this)">

        </div>
        <button id="checkout-btn" class="checkout-button"  style="width: 200px;font-weight: bold;" type="submit">pay</button>

    </div>
                </form>
</div>


    </section>
<!--        
                <div class="card">
                    <img src="assets/tomato.png" class="myimage" alt="Product 1">
                    <div class="card-body">
                        <h5 class="card-title">tomato</h5>
                        <p class="card-text">Price: $19.99</p>
                        <a href="#" class="">more</a>
                    </div>
              <div class="cartcontrols">
                <div>
                    <button class="minus">-</button>
                    <label for="" class="label">0</label>
                    <button class="plus">+</button>

                </div>
                <button class="add">add to cart</button>
              </div>
                </div>
                
            </div>
        </div> -->

    <!-- Reviews Section -->
    <section id="Reviews">
    <br>
    <br>
    <br>
    <br>
    <br>
    <h2 style="margin-left:100px;">Customer Reviews</h2>
    <div id="customerReviewsCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <div class="container">
                
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Customer 1</h5>
                        <p class="card-text">"The products were very nice ,I really loved them"</p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9733; <!-- Five filled stars -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <div class="container">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Customer 2</h5>
                        <p class="card-text">"Nice service.The grocery were very fresh"</p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9733; <!-- Five filled stars -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <div class="container">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Customer 3</h5>
                        <p class="card-text">"I loved the products alot."</p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9733; <!-- Five filled stars -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#customerReviewsCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#customerReviewsCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>


    </section>

    <!-- Contact Section -->
    <section id="Contact">
    <div class="container">
    <h2>Contact Us</h2>
    <p>For any inquiries or assistance, please feel free to contact us.</p>
    <form id="myForm" action="respond.php" method="POST">  <!--removed respond.php in action -->
    <div class="mb-3">
        <label for="NAME" class="form-label">Your Name</label>
        <input type="text" class="form-control" id="name" name="NAME" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input type="email" class="form-control" id="email" name="EMAIL" required>
    </div>
    <div class="mb-3">
        <label for="message" class="form-label">Message</label>
        <textarea class="form-control" id="message" name="MESSAGE" rows="3" required></textarea>
    </div>
    <button type="submit" name="submit" class="mybutton btnorange">Submit</button>
</form>
            <p>Contact Information:</p>
            <p>Email: <a href="mailto:codjecinta@gmail.com">codjecinta@gmail.com</a></p>
            <p>Phone: <a href="tel:+254759419486">+254-759-419-486</a></p>
        </div>
    </section>
     <!-- Footer section -->
     <footer>
        <div class="social-icons">
            <a href="https://www.facebook.com"><i class="fab fa-facebook"></i></a>
            <a href="https://twitter.com"><i class="fab fa-twitter"></i></a>
            <a href="https://www.instagram.com"><i class="fab fa-instagram"></i></a>
            <!-- Add more social media icons as needed -->
        </div>
    </footer>

   
</div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script src="js/grocery.js"></script>
    <script>function validateNumber(input) {
  // Allow optional +254 prefix for international users
  const regex = /^(?:\+254)?\d{10}$/; 
  if (!regex.test(input.value)) {
    input.style.backgroundColor = "red"; // Error indication with red background
  } else {
    input.style.backgroundColor = "white"; // Success indication with white background
  }
}</script>
    <script>
 // for profile view pop up 
 document.addEventListener('DOMContentLoaded', function() {
    var loginBtn = document.getElementById('login-btn');
    var userPopup = document.getElementById('user-popup');
    
    loginBtn.addEventListener('click', function() {
        userPopup.style.display = (userPopup.style.display === 'block') ? 'none' : 'block';
    });

    // Close popup when clicking outside
    document.addEventListener('click', function(event) {
        if (!userPopup.contains(event.target) && event.target !== loginBtn) {
            userPopup.style.display = 'none';
        }
    });
});


        document.addEventListener('DOMContentLoaded', function () {
            let TotalAmount = parseFloat(localStorage.getItem("totalamount")) || 0;
            let cartItemList = JSON.parse(localStorage.getItem("cartItems")) || [];

            const itemsInCart = document.getElementById('itemsincart');
            const cartModal = document.getElementById('cart-modal');
            const cartItems = document.getElementById('cart-items');
            const totalAmountDisplay = document.getElementById('total-amount');
           // const removeButtons = document.querySelectorAll('.remove');
          // const additionButtons = document.querySelectorAll('.addition');
//added the above two constant
            const checkoutBtn = document.getElementById('checkout-btn');

            const plusButtons = document.querySelectorAll('.plus');
            const itemsavailable=document.querySelectorAll('.itemsavailable');
            let newitemsavailable;
            const minusButtons = document.querySelectorAll('.minus');
            const labels = document.querySelectorAll('.label');
            const cartButtons = document.querySelectorAll('.add');

            // Calculate the total quantity of items in cartItemList
            let totalItemsInCart = cartItemList.reduce((total, item) => total + item.quantity, 0);
            itemsInCart.textContent = totalItemsInCart;

            plusButtons.forEach((button, index) => {
                button.addEventListener('click', function () {
                    let value = parseInt(labels[index].textContent);
                    newitemsavailable = parseInt(itemsavailable[index].textContent)
                   console.log(newitemsavailable);

                    if (newitemsavailable >0) {
                    labels[index].textContent = value + 1;
                    newitemsavailable = parseInt(itemsavailable[index].textContent)
                    itemsavailable[index].textContent =newitemsavailable - 1;
                    }
                    else{
                        console.log("operation not allowed");
                    }
                });
            });

            minusButtons.forEach((button, index) => {
                button.addEventListener('click', function () {
                    let value = parseInt(labels[index].textContent);
                 
                    if (value > 0) {
                        newitemsavailable = parseInt(itemsavailable[index].textContent)
                        itemsavailable[index].textContent =newitemsavailable + 1;
                        labels[index].textContent = value - 1;
                    }
                    else{
                        console.log("operation not allowed");
                    }
                });
            });

            cartButtons.forEach((button, index) => {
                button.addEventListener('click', function () {
                    const productName = this.dataset.productName;
                    let productPrice = parseFloat(this.dataset.productPrice);
                    const quantity = parseInt(labels[index].textContent);
                    const totalPrice = productPrice * quantity;

                    // Check if the product is already in cartItemList
                    const existingItemIndex = cartItemList.findIndex(item => item.productName === productName);

                    if (existingItemIndex !== -1) {
                        // Update existing item's quantity and total price
                        cartItemList[existingItemIndex].quantity += quantity;
                        cartItemList[existingItemIndex].totalPrice += totalPrice;
                    } else {
                        // Add new item to cartItemList
                        cartItemList.push({
                            productName: productName,
                            quantity: quantity,
                            totalPrice: totalPrice
                        });
                    }

                    // Update TotalAmount
                    TotalAmount += totalPrice;

                    // Update localStorage
                    localStorage.setItem("cartItems", JSON.stringify(cartItemList));
                    localStorage.setItem("totalamount", TotalAmount);

                    // Update total quantity of items in cart span
                    totalItemsInCart += quantity;
                    itemsInCart.textContent = totalItemsInCart;

                    // Update UI
                    labels[index].textContent = 0;
                    displayCartItems();
                });
            });
          

            document.getElementById('cart-btn').addEventListener('click', function () {
                cartModal.style.display = 'block';
                document.getElementById('total').innerHTML =  localStorage.getItem("totalamount", TotalAmount);
                 document.getElementById('stktotal').value =  localStorage.getItem("totalamount", TotalAmount);
            });

            document.querySelector('.close').addEventListener('click', function () {
                cartModal.style.display = 'none';
            });

            checkoutBtn.addEventListener('click', function () {
                // Implement checkout functionality here
            });
            
            function displayCartItems() {
                
                cartItems.innerHTML = '';
                //  added attributes to these buttons addition and remove
                cartItemList.forEach(item => {
                    const itemHTML = `
                        <div class="cart-item">
                            <p>${item.productName}</p>
                            <p>Quantity: ${item.quantity}</p>
                            <p>Total Price: ${item.totalPrice.toFixed(2)}</p>
                        </div>
                    `;
                    cartItems.insertAdjacentHTML('beforeend', itemHTML);
                });
                totalAmountDisplay.textContent = TotalAmount.toFixed(2);
            }

            displayCartItems(); // Initial display
        });

        // Get the current date and time
const currentTimestamp = new Date().toLocaleString();

// Update the timestamp in the HTML element
document.getElementById('timestamp').textContent = currentTimestamp;

        // Add event listener to radio buttons
document.querySelectorAll('input[name="payment-method"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        updateCheckoutButton(this.value);
    });
});


    // Function to update checkout button text based on selected payment method
    function updateCheckoutButton(paymentMethod) {
        let checkoutButton = document.getElementById('checkout-btn');
        switch (paymentMethod) {
            case 'mpesa':
                checkoutButton.textContent = 'Checkout with Mpesa';
                break;
            case 'paypal':
                checkoutButton.textContent = 'Checkout with PayPal';
                break;
            case 'visa':
                checkoutButton.textContent = 'Checkout with Visa';
                break;
            default:
                checkoutButton.textContent = 'Checkout';
        }
    }


    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
     // Define cartItems and cartModal globally
const cartItems = document.getElementById('cart-items');
const cartModal = document.getElementById('cart-modal');

$(document).ready(function() {
    $('#checkout-btn').click(function() {
        // Retrieve cartItems from localStorage
        var cartItemsData = JSON.parse(localStorage.getItem('cartItems'));
        if (!cartItems || cartItems.length === 0) {
            alert('Your cart is empty. Please add items before checking out.');
            return; // Stop execution if cart is empty
        }else{
        // Serialize cart data
        var cartData = JSON.stringify(cartItemsData);

        // Get current month and year
        var currentMonth = new Date().getMonth() + 1; // Month (1-12)
        var currentYear = new Date().getFullYear(); // Full four-digit year

        // Additional data
        var customerId = "test@gmail.com";
        var totalAmount= localStorage.getItem('totalamount');
        var paymentType = "Mpesa";
        var paymentId = "WDVK23VJF83";

        // AJAX request to send data to PHP script
        $.ajax({
            type: 'POST',
            url: 'grocery.php',
            data: {
                cartData: cartData,
                month: currentMonth,
                year: currentYear,
                customerId: customerId,
                totalAmount: totalAmount,
                paymentType: paymentType,
                paymentId: paymentId
            },
            success: function(response) {
                console.log(response); // Log response from PHP script
                
                // Handle success response if needed
                
                // Clear the local storage
                localStorage.removeItem('cartItems');
                localStorage.removeItem('totalamount');

                // Clear the cart items display
                cartItems.innerHTML = '';

                // Close the cart popup
                cartModal.style.display = 'none';

                // Show success message
                const successMessage = document.createElement('div');
                successMessage.textContent = 'Enter your pin to complete the payment!';
                successMessage.style.color = 'green';
                successMessage.style.fontSize = '24px'; 
                successMessage.style.position = 'fixed';
                successMessage.style.top = '50%';
                successMessage.style.left = '50%';
                successMessage.style.transform = 'translate(-50%, -50%)';
                successMessage.style.backgroundColor = 'white';
                successMessage.style.padding = '80px';
                successMessage.style.borderRadius = 'f80px';
                successMessage.style.boxShadow = '0 0 10px rgba(0, 0, 0, 0.3)';
                document.body.appendChild(successMessage);

                // Hide the success message after 5 seconds
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 10000);
            },
            error: function(xhr, status, error) {
                console.error(error);  // Log error message
                // Handle error if needed
            }
        });
    }
    });
});

    </script>
</head>
</body>

</html>