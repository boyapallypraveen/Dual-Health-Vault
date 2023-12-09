<?php
session_start(); // Start the session
/*
$fullName = $_POST['fullName'];
$emailPhoneSignup = $_POST['emailPhoneSignup'];
$passwordSignup = $_POST['passwordSignup'];

$conn = new mysqli('localhost:3307', 'root', '', 'mysql');

if ($conn->connect_error) {
    echo "$conn->connect_error";
    die("Connection Failed : " . $conn->connect_error);
} else {
    $stmt = $conn->prepare(" insert into patient(fullName, emailPhoneSignup, passwordSignup) VALUES (?,?,?)");
    $stmt->bind_param("sss", $fullName, $emailPhoneSignup, $passwordSignup);

    $execval = $stmt->execute();

    if ($execval) {
        // Data inserted successfully, redirect to the main page
        header("Location: /hello/main.html");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} 
*/


$fullName = $_POST['fullName'];
$emailPhoneSignup = $_POST['emailPhoneSignup'];
$passwordSignup = $_POST['passwordSignup'];
$table = $_POST['tableInput'];
$pos = strpos($emailPhoneSignup, '@');
echo $table;
if ($pos !== false) {
    $emailPhoneSignup = substr($emailPhoneSignup, 0, $pos); // Update the value of $emailPhoneSignup to contain the cleaned email
}

$conn = new mysqli('localhost:3307', 'root', '', 'mysql');

if($table == "Patient"){

if ($conn->connect_error) {
    echo "$conn->connect_error";
    die("Connection Failed : " . $conn->connect_error);
} else {
    $stmt = $conn->prepare("insert into patient(fullName, emailPhoneSignup, passwordSignup) VALUES (?,?,?)");
    $stmt->bind_param("sss", $fullName, $emailPhoneSignup, $passwordSignup);

    $execval = $stmt->execute();

    if ($execval) {
        // Data inserted successfully, now create a new table for the user
        //$tableName = $emailPhoneSignup; // Use the email/phone as the table name

        // SQL to create the new table with the specified columns
        // $createTableSQL = "CREATE TABLE $tableName (
        //     id INT AUTO_INCREMENT PRIMARY KEY,
        //     entryDate DATE,
        //     entryTime TIME,
        //     doctorName VARCHAR(255),
        //     hospitalName VARCHAR(255),
        //     files VARCHAR(255),
        //     prescription TEXT
        // )";

        // if ($conn->query($createTableSQL) === TRUE) {
        //     // Table created successfully, redirect to the main page
        //     include('/hello/main.html');
        //     header("Location: /hello/main.html?emailPhoneSignup=" . $emailPhoneSignup);
        //     exit;
        // } else {
        //     echo "Error creating table: " . $conn->error;
        // }
        $_SESSION['emailPhoneSignup'] = $emailPhoneSignup;
        header("Location: /hello/patientmain.html");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}



}
else if($table == "Doctor")
{

if ($conn->connect_error) {
    echo "$conn->connect_error";
    die("Connection Failed : " . $conn->connect_error);
} else {
    $stmt = $conn->prepare("insert into doctor(fullName, emailPhoneSignup, passwordSignup) VALUES (?,?,?)");
    $stmt->bind_param("sss", $fullName, $emailPhoneSignup, $passwordSignup);

    $execval = $stmt->execute();

    if ($execval) {
        // Data inserted successfully, now create a new table for the user
        //$tableName = $emailPhoneSignup; // Use the email/phone as the table name

        // SQL to create the new table with the specified columns
        // $createTableSQL = "CREATE TABLE $tableName (
        //     id INT AUTO_INCREMENT PRIMARY KEY,
        //     entryDate DATE,
        //     entryTime TIME,
        //     doctorName VARCHAR(255),
        //     hospitalName VARCHAR(255),
        //     files VARCHAR(255),
        //     prescription TEXT
        // )";

        // if ($conn->query($createTableSQL) === TRUE) {
        //     // Table created successfully, redirect to the main page
        //     include('/hello/main.html');
        //     header("Location: /hello/main.html?emailPhoneSignup=" . $emailPhoneSignup);
        //     exit;
        // } else {
        //     echo "Error creating table: " . $conn->error;
        // }
        $_SESSION['emailPhoneSignup'] = $emailPhoneSignup;
        header("Location: /hello/doctormain.html");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}



}




/*
$fullName = $_POST['fullName'];
$emailPhoneSignup = $_POST['emailPhoneSignup'];
$passwordSignup = $_POST['passwordSignup'];
$table = $_POST['tableInput'];
$pos = strpos($emailPhoneSignup, '@');
echo $table;

// Generate a random secret key and IV
$secretKey = bin2hex(random_bytes(32)); // 32 bytes for AES-256
$iv = bin2hex(random_bytes(16)); // 16 bytes for AES-256-CBC

if ($pos !== false) {
    $emailPhoneSignup = substr($emailPhoneSignup, 0, $pos); // Update the value of $emailPhoneSignup to contain the cleaned email
}

$conn = new mysqli('localhost:3307', 'root', '', 'mysql');

if ($table == "Patient") {

    if ($conn->connect_error) {
        echo "$conn->connect_error";
        die("Connection Failed : " . $conn->connect_error);
    } else {
        // Insert user details along with the secret key and IV into the patient table
        $stmt = $conn->prepare("INSERT INTO patient(fullName, emailPhoneSignup, passwordSignup, secretKey, iv) VALUES (?,?,?,?,?)");
        $hashedPassword = password_hash($passwordSignup, PASSWORD_DEFAULT); // Hash the password before storing

        $stmt->bind_param("sssss", $fullName, $emailPhoneSignup, $hashedPassword, $secretKey, $iv);

        $execval = $stmt->execute();

        if ($execval) {
            $_SESSION['emailPhoneSignup'] = $emailPhoneSignup;
            // Redirect to the appropriate main page (patientmain.html in this case)
            header("Location: /hello/patientmain.html");
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
} else if ($table == "Doctor") {

    if ($conn->connect_error) {
        echo "$conn->connect_error";
        die("Connection Failed : " . $conn->connect_error);
    } else {
        // Insert user details along with the secret key and IV into the doctor table
        $stmt = $conn->prepare("INSERT INTO doctor(fullName, emailPhoneSignup, passwordSignup, secretKey, iv) VALUES (?,?,?,?,?)");
        $hashedPassword = password_hash($passwordSignup, PASSWORD_DEFAULT); // Hash the password before storing

        $stmt->bind_param("sssss", $fullName, $emailPhoneSignup, $hashedPassword, $secretKey, $iv);

        $execval = $stmt->execute();

        if ($execval) {
            $_SESSION['emailPhoneSignup'] = $emailPhoneSignup;
            // Redirect to the appropriate main page (doctormain.html in this case)
            header("Location: /hello/doctormain.html");
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
}
*/



?>


