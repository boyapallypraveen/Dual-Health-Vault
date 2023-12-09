


<?php

session_start(); // Start the session (if not already started)

$emailPhoneSignup = $_SESSION['emailPhoneSignup'];

$doctorName = $_POST['doctorName'];
$hospitalName = $_POST['hospitalName'];
$files = $_POST['file'];
$prescription = $_POST['prescription'];

$shift = 3;

// Function to encrypt using Caesar cipher
function caesarCipherEncrypt($text, $shift) {
    $encryptedText = '';
    $text = strtolower($text); // Convert to lowercase for encryption

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
$doctorName = caesarCipherEncrypt($doctorName, $shift);
$hospitalName = caesarCipherEncrypt($hospitalName, $shift);
$prescription = caesarCipherEncrypt($prescription, $shift);


if (isset($_FILES['file'])) {
    // Initialize an array to store file details
    $fileDetails = [];

    // Specify the destination folder
    $uploadFolder = 'C:\xampp\htdocs\hello\images/'; // Make sure the "images" folder exists

    foreach ($_FILES['file']['name'] as $key => $fileName) {
        $fileTmpName = $_FILES['file']['tmp_name'][$key];
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = uniqid('file_') . ".$fileExt";
        $destination = $uploadFolder . $newFileName;

        if (move_uploaded_file($fileTmpName, $destination)) {
            // Store both the original file name and the new file location
            $fileDetails[] = ['name' => $fileName, 'location' => $destination];
        } else {
            echo "Error uploading file: $fileName";
        }
    }
}


$conn = new mysqli('localhost:3307', 'root', '', 'mysql');

if ($conn->connect_error) {
    echo "$conn->connect_error";
    die("Connection Failed : " . $conn->connect_error);
} else {
    // First, query the patient table to get the id
    $query = "SELECT id FROM patient WHERE emailPhoneSignup = ?";
    
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
        $query = "INSERT INTO treatment(p_id, doctorName, hospitalName, files, prescription) VALUES (?,?,?,?,?)";
        
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            die('Error: ' . $conn->error);
        }
        $uploadedFileLocations = array_column($fileDetails, 'location');
        $files = implode(', ', $uploadedFileLocations);

        $stmt->bind_param("issss", $p_id, $doctorName, $hospitalName, $files, $prescription);

        $execval = $stmt->execute();

        if ($execval) {
            // Data inserted successfully, redirect to the main page
            header("Location:/hello/patientmain.html");
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


/*
session_start(); // Start the session (if not already started)

$emailPhoneSignup = $_SESSION['emailPhoneSignup'];

$doctorName = $_POST['doctorName'];
$hospitalName = $_POST['hospitalName'];
$files = $_POST['file'];

$prescription = $_POST['prescription'];

// Function to encrypt data using AES and base64 encode
function aesEncrypt($data, $key, $iv) {
    return base64_encode(openssl_encrypt($data, 'aes-256-cbc', $key, 0, hex2bin($iv)));
}

$conn = new mysqli('localhost:3307', 'root', '', 'mysql');

if ($conn->connect_error) {
    echo "$conn->connect_error";
    die("Connection Failed: " . $conn->connect_error);
} else {
    // First, query the patient table to get the secret key and IV
    $query_secret_iv = "SELECT id, secretKey, iv FROM patient WHERE emailPhoneSignup = ?";
    
    $stmt_secret_iv = $conn->prepare($query_secret_iv);

    if ($stmt_secret_iv === false) {
        die('Error: ' . $conn->error);
    }

    $stmt_secret_iv->bind_param("s", $emailPhoneSignup);

    $stmt_secret_iv->execute();

    $stmt_secret_iv->bind_result($p_id, $secretKey, $iv);

    if ($stmt_secret_iv->fetch()) {
        // The query was successful, and $p_id, $secretKey, $iv now contain the patient's ID, secret key, and IV.
        $stmt_secret_iv->close();
        $fileDetails = [];

        if (isset($_FILES['file'])) {
            // Specify the destination folder
            $uploadFolder = 'C:\xampp\htdocs\hello\images/'; // Make sure the "images" folder exists

            foreach ($_FILES['file']['name'] as $key => $fileName) {
                $fileTmpName = $_FILES['file']['tmp_name'][$key];
                $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
                $newFileName = uniqid('file_') . ".$fileExt";
                $destination = $uploadFolder . $newFileName;

                // Read the file content
                $fileContent = file_get_contents($fileTmpName);

                // Encrypt the base64-encoded file content using AES
                $encryptedContent = aesEncrypt(base64_encode($fileContent), $secretKey, $iv);

                // Save the encrypted content to the destination file
                file_put_contents($destination, $encryptedContent);

                // Store both the original file name and the new file location
                $fileDetails[] = ['name' => $fileName, 'location' => $destination];
            }
        }

        // Encrypt prescription
        $encryptedPrescription = aesEncrypt($prescription, $secretKey, $iv);

        // Now, insert the treatment history with p_id = id
        $query_insert = "INSERT INTO treatment(p_id, doctorName, hospitalName, files, prescription) VALUES (?,?,?,?,?)";

        $stmt_insert = $conn->prepare($query_insert);

        if ($stmt_insert === false) {
            die('Error: ' . $conn->error);
        }

        $uploadedFileLocations = array_column($fileDetails, 'location');
        $files = implode(', ', $uploadedFileLocations);

        $stmt_insert->bind_param("issss", $p_id, $doctorName, $hospitalName, $files, $encryptedPrescription);

        $execval = $stmt_insert->execute();

        if ($execval) {
            // Data inserted successfully, redirect to the main page
            header("Location:/hello/patientmain.html");
            exit;
        } else {
            echo "Error: " . $stmt_insert->error;
        }

    } else {
        echo "Patient not found for emailPhoneSignup: " . $emailPhoneSignup;
    }

    $stmt_secret_iv->close();
    $conn->close();
}
*/
?>
