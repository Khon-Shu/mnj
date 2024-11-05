<?php 

session_start(); // Start the session at the beginning of the script

include 'connect.php';

// Sign Up Logic
if (isset($_POST['signUp'])) {
    $firstName = $_POST['fname'];
    $lastName = $_POST['lname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpass = $_POST['cpass'];

    // Define an array for warning messages
    $warning_msg = array();

    // Validation for First Name and Last Name (3 to 15 characters)
    if (strlen($firstName) < 3 || strlen($firstName) > 15 || strlen($lastName) < 3 || strlen($lastName) > 15) {
        $warning_msg[] = "First name and last name must be between 3 and 15 characters!";
    }

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $warning_msg[] = "Invalid email address!";
    }

    // Password validation (up to 10 characters)
    if (strlen($password) > 10) {
        $warning_msg[] = "Password must be up to 10 characters!";
    }

    // Check if the password and confirm password match
    if ($password !== $cpass) {
        $warning_msg[] = "Passwords do not match!";
    }

    // Display warning messages if any
    if (!empty($warning_msg)) {
        $allWarnings = implode("\\n", $warning_msg); // Combine all warnings with line breaks
        echo "<script>alert('$allWarnings');</script>";
        exit();
    }

    // Hash the password using MD5 (consider using a stronger hash algorithm like password_hash for better security)
    $hashedPassword = md5($password);

    // Check if email already exists in the database
    $checkEmail = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($checkEmail);
    if ($result->num_rows > 0) {
        echo "<script>alert('Email Address Already Exists!');</script>";
    } else {
        // Insert query if all validations pass
        $insertQuery = "INSERT INTO users (firstName, lastName, email, password, usertype)
                        VALUES ('$firstName', '$lastName', '$email', '$hashedPassword', 'user')";
        if ($conn->query($insertQuery) === TRUE) {
            echo "<script>alert('Registration successful!'); window.location.href = 'index.php';</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
}

// Sign In Logic
if (isset($_POST['signIn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashedPassword = md5($password); // Hash the password

    // Prepare the SQL query
    $sql = "SELECT * FROM users WHERE email='$email' AND password='$hashedPassword'";
    $result = $conn->query($sql);

    if ($result === false) {
        // Debugging: Check for SQL errors
        echo "SQL Error: " . $conn->error;
    } else if ($result->num_rows > 0) {
        // Fetch user data
        $row = $result->fetch_assoc();
        
        // Store user information in session
        $_SESSION['user_id'] = $row['Id'];   // Store the user_id from the database
        $_SESSION['email'] = $row['email'];

        // Redirect based on usertype
        if ($row['usertype'] === 'admin') {
            header("Location: admin/admin-panel.html");
        } else {
            header("Location: homepage/home.html");
        }
        exit();
    } else {
        // Incorrect email or password
        echo "<script type='text/javascript'>alert('Incorrect Email or Password');window.location.href = 'index.php';</script>";
    }
}
