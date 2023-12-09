


<?php
session_start(); // Start the session (if not already started)

$emailPhoneSignup = $_SESSION['emailPhoneSignup'];
$entryDate = $_POST['entryDate'];
$entryTime = $_POST['entryTime'];
$doctorName = $_POST['doctorName'];
$hospitalName = $_POST['hospitalName'];
$files = $_POST['docpicker'];
$prescription = $_POST['textfield'];
//include '/hello/login.php'
//$emailPhoneSignup = $_POST['emailPhoneSignup']; // Retrieve the table name from the URL
/*
if (isset($_FILES['docpicker'])) {
    $files = $_FILES['docpicker'];

    // Extract file information
    $fileNames = $files['name'];
    $fileTmpNames = $files['tmp_name'];

    // Array to store uploaded file paths
    $uploadedFiles = [];

    // Specify the destination folder
    $uploadFolder = 'C:\xampp\htdocs\hello\images/'; // Make sure the "images" folder exists

    // Loop through the uploaded files
    foreach ($fileTmpNames as $key => $fileTmpName) {
        $fileName = $fileNames[$key];
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);

        // Generate a unique name for each file to avoid overwriting
        $newFileName = uniqid('file_') . ".$fileExt";

        // Build the full path to the destination folder
        $destination = $uploadFolder . $newFileName;

        if (move_uploaded_file($fileTmpName, $destination)) {
            $uploadedFiles[] = $destination;
        } else {
            echo "Error uploading file: $fileName";
        }
    }

    // Check if at least one file was uploaded
    if (count($uploadedFiles) > 0) {
        $files = implode(', ', $uploadedFiles); // Store the uploaded file paths as a comma-separated string
    } else {
        $files = ''; // No files uploaded
    }
}
*/


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


if (isset($_FILES['docpicker'])) {
    // Initialize an array to store file details
    $fileDetails = [];

    // Specify the destination folder
    $uploadFolder = 'C:\xampp\htdocs\hello\images/'; // Make sure the "images" folder exists

    foreach ($_FILES['docpicker']['name'] as $key => $fileName) {
        $fileTmpName = $_FILES['docpicker']['tmp_name'][$key];
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
        $query = "INSERT INTO treatment_history(p_id, entryDate, entryTime, doctorName, hospitalName, files, prescription) VALUES (?,?,?,?,?,?,?)";
        
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            die('Error: ' . $conn->error);
        }
        $uploadedFileLocations = array_column($fileDetails, 'location');
        $files = implode(', ', $uploadedFileLocations);

        $stmt->bind_param("issssss", $p_id, $entryDate, $entryTime, $doctorName, $hospitalName, $files, $prescription);

        $execval = $stmt->execute();

        if ($execval) {
            // Data inserted successfully, redirect to the main page
            header("Location:/hello/main.html");
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
?>
