<?php

session_start();

$emailPhoneSignup = $_SESSION['emailPhoneSignup'];
$fullName = $_POST['fullName'];
$mobileNumber = $_POST['mobileNumber'];
$address = $_POST['address'];
$previousObservations = $_POST['previousObservations'];
$prescription = $_POST['prescription'];

$shift = 3;

// Function to encrypt using Caesar cipher
function caesarCipherEncrypt($text, $shift) {
    $encryptedText = '';
    $text = strtolower($text);

    for ($i = 0; $i < strlen($text); $i++) {
        $char = $text[$i];
        if (ctype_alpha($char)) {
            $encryptedChar = chr((ord($char) - 97 + $shift) % 26 + 97);
        } else {
            $encryptedChar = $char;
        }
        $encryptedText .= $encryptedChar;
    }

    return $encryptedText;
}

// Encrypt the specified fields
$fullName = caesarCipherEncrypt($fullName, $shift);
$mobileNumber = caesarCipherEncrypt($mobileNumber, $shift);
$address = caesarCipherEncrypt($address, $shift);
$previousObservations = caesarCipherEncrypt($previousObservations, $shift);
$prescription = caesarCipherEncrypt($prescription, $shift);

$conn = new mysqli('localhost:3307', 'root', '', 'mysql');

if ($conn->connect_error) {
    echo "$conn->connect_error";
    die("Connection Failed : " . $conn->connect_error);
} else {
    // First, query the doctor table to get the id
    $query = "SELECT id FROM doctor WHERE emailPhoneSignup = ?";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die('Error: ' . $conn->error);
    }

    $stmt->bind_param("s", $emailPhoneSignup);
    $stmt->execute();
    $stmt->bind_result($id);

    if ($stmt->fetch()) {
        // The query was successful, and $id now contains the doctor's ID.
        $stmt->close();
        $p_id = $id;

        // Now, retrieve the previous hash
        $query = "SELECT MAX(id), presentHash FROM hospital_data WHERE p_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            die('Error: ' . $conn->error);
        }

        $stmt->bind_param("i", $p_id);
        $stmt->execute();
        $stmt->bind_result($previousId, $previousHash);

        if ($stmt->fetch()) {
            $stmt->close();

            // Calculate the present hash
            $presentHash = hash('sha256', $previousHash . $fullName . $mobileNumber . $address . $previousObservations . $prescription);

            // Now, insert the treatment history with p_id = id and the calculated hashes
            $query = "INSERT INTO hospital_data (fullName, mobileNumber, address, previousObservations, prescription, p_id, previousHash, presentHash) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);

            if ($stmt === false) {
                die('Error: ' . $conn->error);
            }

            // Ensure that none of the values are NULL
            $previousHash = $previousHash ?? ''; // Set a default value if it's NULL
            $presentHash = $presentHash ?? ''; // Set a default value if it's NULL

            $stmt->bind_param("sssssiis", $fullName, $mobileNumber, $address, $previousObservations, $prescription, $p_id, $previousHash, $presentHash);
            $execval = $stmt->execute();

            if ($execval) {
                // Data inserted successfully, redirect to the main page
                header("Location:/hello/doctormain.html");
                exit;
            } else {
                echo "Error: " . $stmt->error;
            }
        } else {
            echo "Error fetching previous hash: " . $stmt->error;
        }
    } else {
        echo "Doctor not found for emailPhoneSignup: " . $emailPhoneSignup;
    }

    $stmt->close();
    $conn->close();
}



/*   Remove this.

session_start();

$emailPhoneSignup = $_SESSION['emailPhoneSignup'];
$fullName = $_POST['fullName'];
$mobileNumber = $_POST['mobileNumber'];
$address = $_POST['address'];
$previousObservations = $_POST['previousObservations'];
$prescription = $_POST['prescription'];

$shift = 3;

// Function to encrypt using Caesar cipher
function caesarCipherEncrypt($text, $shift) {
    $encryptedText = '';
    $text = strtolower($text);

    for ($i = 0; $i < strlen($text); $i++) {
        $char = $text[$i];
        if (ctype_alpha($char)) {
            $encryptedChar = chr((ord($char) - 97 + $shift) % 26 + 97);
        } else {
            $encryptedChar = $char;
        }
        $encryptedText .= $encryptedChar;
    }

    return $encryptedText;
}

// Encrypt the specified fields
$fullName = caesarCipherEncrypt($fullName, $shift);
$mobileNumber = caesarCipherEncrypt($mobileNumber, $shift);
$address = caesarCipherEncrypt($address, $shift);
$previousObservations = caesarCipherEncrypt($previousObservations, $shift);
$prescription = caesarCipherEncrypt($prescription, $shift);

$conn = new mysqli('localhost:3307', 'root', '', 'mysql');

Remove this */  
/*
if ($conn->connect_error) {
    echo "$conn->connect_error";
    die("Connection Failed : " . $conn->connect_error);
} else {
    // Now, insert the treatment history with p_id = id
    $query = "INSERT INTO hospital_data(fullName, mobileNumber, address, previousObservations, prescription) VALUES (?,?,?,?,?)";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die('Error: ' . $conn->error);
    }

    $stmt->bind_param("sssss", $fullName, $mobileNumber, $address, $previousObservations, $prescription);

    $execval = $stmt->execute();

    if ($execval) {
        // Data inserted successfully, redirect to the main page
        header("Location:/hello/doctormain.html");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
*/

/* Remove this
if ($conn->connect_error) {
    echo "$conn->connect_error";
    die("Connection Failed : " . $conn->connect_error);
} else {
    // First, query the patient table to get the id
    $query = "SELECT id FROM doctor WHERE emailPhoneSignup = ?";
    
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die('Error: ' . $conn->error);
        
    }

    $stmt->bind_param("s", $emailPhoneSignup);

    $stmt->execute();

    $stmt->bind_result($id);

    if ($stmt->fetch()) {
        // The query was successful, and $id now contains the patient's ID.
        $stmt->close();
        $p_id = $id; // I said you missed this
        // Now, insert the treatment history with p_id = id
        $query = "INSERT INTO hospital_data(fullName, mobileNumber, address, previousObservations, prescription,p_id) VALUES (?,?,?,?,?,?)";
        
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            die('Error: ' . $conn->error);
        }
        $stmt->bind_param("sssssi", $fullName, $mobileNumber, $address, $previousObservations, $prescription,$p_id);

        $execval = $stmt->execute();

        if ($execval) {
            // Data inserted successfully, redirect to the main page
            header("Location:/hello/doctormain.html");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Patient not found for emailPhoneSignup: " . $emailPhoneSignup;
    }

    $stmt->close();
    $conn->close();
}

 Remove this */ 
?>

