<?php
session_start(); // Start the session (if not already started)

$emailPhoneSignup = $_SESSION['emailPhoneSignup'];

// Connect to the database
$conn = new mysqli('localhost:3307', 'root', '', 'mysql');
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// Query to retrieve the patient's ID
$query_patient = "SELECT id FROM patient WHERE emailPhoneSignup = ?";
$stmt_patient = $conn->prepare($query_patient);
$stmt_patient->bind_param("s", $emailPhoneSignup);
$stmt_patient->execute();
$stmt_patient->bind_result($patient_id);

if ($stmt_patient->fetch()) {
    $stmt_patient->close();

    // Query to retrieve treatment history entries for the patient
    $query_entries = "SELECT entryDate, entryTime, doctorName, hospitalName, files, prescription FROM treatment_history WHERE p_id = ? ORDER BY entryDate DESC, entryTime DESC";
    $stmt_entries = $conn->prepare($query_entries);
    $stmt_entries->bind_param("i", $patient_id);
    $stmt_entries->execute();
    $result = $stmt_entries->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Decrypt the fields (reverse Caesar cipher)
            $doctorName = caesarCipherDecrypt($row['doctorName'], -3);
            $hospitalName = caesarCipherDecrypt($row['hospitalName'], -3);
            $prescription = caesarCipherDecrypt($row['prescription'], -3);

            // Display the entries
            echo '<div class="entry-container">';
            $filePath = str_replace('/', '\\', $row['files']);
            echo '<div class="entry-image"><img src="' . str_replace('C:\\xampp\\htdocs', '', $filePath) . '" alt="Entry Image" width="40%" height="40%"></div>';
            //echo $filePath;
            echo '<div class="entry-details">';
            echo '<p>Entry Date: ' . $row['entryDate'] . '</p>';
            echo '<p>Entry Time: ' . $row['entryTime'] . '</p>';
            echo '<p>Doctor Name: ' . $doctorName . '</p>'; // Use the decrypted doctorName
            echo '<p>Hospital Name: ' . $hospitalName . '</p>'; // Use the decrypted hospitalName
            echo '<p>Prescription: ' . $prescription . '</p>'; // Use the decrypted prescription
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo "No entries found for this patient.";
    }

    $stmt_entries->close();
} else {
    echo "Patient not found for emailPhoneSignup: " . $emailPhoneSignup;
}


function caesarCipherDecrypt($text, $shift) {
    $decryptedText = '';

    for ($i = 0; $i < strlen($text); $i++) {
        $char = $text[$i];
        if (ctype_alpha($char)) {
            $isUpperCase = ctype_upper($char);
            $char = strtolower($char);
            $decryptedChar = chr(((ord($char) - 97 - (-$shift) + 26) % 26) + 97);
            if ($isUpperCase) {
                $decryptedChar = strtoupper($decryptedChar);
            }
        } else {
            $decryptedChar = $char;
        }
        $decryptedText .= $decryptedChar;
    }

    return $decryptedText;
}


$conn->close();

?>


<!DOCTYPE html>
<html>
<style>
form {
    display: flex;
    justify-content: center;
    margin-top: 20px; /* Add some spacing above the button */
}

input[type="submit"] {
    background-color: #007BFF;
    color: #fff;
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

input[type="submit"]:hover {
    background-color: #0056b3;
}
</style>
<head>
    <!-- Your head content here -->
</head>
<body>
    <!-- Content for displaying the entries here -->
    
    <!-- Add a "Back" button -->
    <form method="get" action="main.html">
        <input type="submit" value="Back">
    </form>
</body>
</html>
