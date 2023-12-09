<?php
session_start();
// Database connection parameters
$host = "localhost:3307";
$username = "root";
$password = "";
$database = "mysql";

// Establish a database connection
$db = new mysqli($host, $username, $password, $database);

// Check the connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}


// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input
    $emailPhoneSignup = $_POST['emailPhoneSignup'];
    $passwordSignup = $_POST['passwordSignup'];
    $table = $_POST['tableInput'];
    $pos = strpos($emailPhoneSignup, '@');

if ($pos !== false) {
    $emailPhoneSignup = substr($emailPhoneSignup, 0, $pos); // Update the value of $emailPhoneSignup to contain the cleaned email
}

    if($table == "Patient"){

            // SQL query to check login credentials
    $query = "SELECT * FROM patient WHERE emailPhoneSignup = ? AND passwordSignup = ?";

    // Prepare the SQL statement
    $stmt = $db->prepare($query);

    if ($stmt) {
        // Bind the parameters
        $stmt->bind_param("ss", $emailPhoneSignup, $passwordSignup);

        // Execute the statement
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // Check if a user with the given credentials exists
        if ($result->num_rows == 1) {
            // Login successful
            // Redirect to the dashboard or another page
            $_SESSION['emailPhoneSignup'] = $emailPhoneSignup;
            header("Location: /hello/patientmain.html");
            //echo "Success";
            exit();
        } else {
            $error_message = "Invalid login credentials. Please try again.";
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        $error_message = "An error occurred while preparing the statement.";
        echo "Error: " . $stmt->error;
    }


    }
    else if($table == "Doctor")
    {

            // SQL query to check login credentials
    $query = "SELECT * FROM doctor WHERE emailPhoneSignup = ? AND passwordSignup = ?";

    // Prepare the SQL statement
    $stmt = $db->prepare($query);

    if ($stmt) {
        // Bind the parameters
        $stmt->bind_param("ss", $emailPhoneSignup, $passwordSignup);

        // Execute the statement
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // Check if a user with the given credentials exists
        if ($result->num_rows == 1) {
            // Login successful
            // Redirect to the dashboard or another page
            $_SESSION['emailPhoneSignup'] = $emailPhoneSignup;
            header("Location: /hello/doctormain.html");
            //echo "Success";
            exit();
        } else {
            $error_message = "Invalid login credentials. Please try again.";
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        $error_message = "An error occurred while preparing the statement.";
        echo "Error: " . $stmt->error;
    }


    }
    
}

// Close the database connection
$db->close();
?>


