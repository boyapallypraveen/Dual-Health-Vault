
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

.menu-bar {
    display: flex;
    justify-content: space-between;
    align-items: center; /* Center vertically */
    padding: 10px;
    background-color: #3498db;
    color: #fff;
}

.search-container {
    display: flex;
    align-items: center;
    /*margin-left: 0px;*/ /* Push the search container to the right */
    margin-right: 500px;
}

.dropdown {
    margin-left: 500px; /* Push the dropdown to the left */
    flex-grow: 1; /* Grow to fill the available space and push the next items to the right */
}

.search-input {
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-right: 5px;
}

.search-button {
    background-color: #2ecc71;
    color: #fff;
    border: none;
    border-radius: 5px;
    padding: 8px;
    cursor: pointer;
}

.search-button:hover {
    background-color: #27ae60;
}

.dropdown select {
    width: 50%; /* Full width */
}
</style>
<script>
    function searchEntries() {
        var searchOption = document.getElementById('search-options').value;
        var searchValue = document.getElementById('search-input').value;

            // Redirect to the PHP page with search parameters
        window.location.href = 'docview.php?searchOption=' + searchOption + '&searchValue=' + searchValue;
    }
</script>
<head>
    <!-- Your head content here -->
</head>
<body>

    <div class="menu-bar">
        <div class="dropdown">
            <select id="search-options">
                <option value="date_time">By Date</option>
                <option value="mobileNumber">By Mobile</option>
            </select>
        </div>
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Search...">
            <button class="search-button" onclick="searchEntries()">Search</button>
        </div>
    </div>
    <!-- Content for displaying the entries here -->

<?php
session_start(); // Start the session (if not already started)

$emailPhoneSignup = $_SESSION['emailPhoneSignup'];

// Connect to the database
$conn = new mysqli('localhost:3307', 'root', '', 'mysql');
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// Query to retrieve the patient's ID
$query_patient = "SELECT id FROM doctor WHERE emailPhoneSignup = ?";
$stmt_patient = $conn->prepare($query_patient);
$stmt_patient->bind_param("s", $emailPhoneSignup);
$stmt_patient->execute();
$stmt_patient->bind_result($patient_id);

if ($stmt_patient->fetch()) {
    $stmt_patient->close();

    // Check if search form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['searchOption']) && isset($_GET['searchValue'])) {
        $searchOption = $_GET['searchOption'];
        $searchValue = $_GET['searchValue'];

        // Query to retrieve treatment history entries based on search option and value
        $query_entries = "SELECT fullName, mobileNumber, address, previousObservations, prescription, date_time FROM hospital_data WHERE p_id = ? AND $searchOption = ? ORDER BY date_time DESC";
        $stmt_entries = $conn->prepare($query_entries);
        $stmt_entries->bind_param("ss", $patient_id, $searchValue);
    } else {
        // Query to retrieve all treatment history entries for the patient
        $query_entries = "SELECT fullName, mobileNumber, address, previousObservations, prescription, date_time FROM hospital_data WHERE p_id = ? ORDER BY date_time DESC";
        $stmt_entries = $conn->prepare($query_entries);
        $stmt_entries->bind_param("s", $patient_id);
    }

    $stmt_entries->execute();
    $result = $stmt_entries->get_result();

    if ($result->num_rows > 0) {
        $borderColors = ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6']; // You can add more colors if needed
        $index = 0;
        while ($row = $result->fetch_assoc()) {
            // Decrypt the fields (reverse Caesar cipher)
            $fullName = caesarCipherDecrypt($row['fullName'], -3);
            $address = caesarCipherDecrypt($row['address'], -3);
            $previousObservations = caesarCipherDecrypt($row['previousObservations'], -3);
            $prescription = caesarCipherDecrypt($row['prescription'], -3);

            // Display the entries
            echo '<div class="entry-container" style="border: 2px solid ' . $borderColors[$index] . '; padding: 10px; margin-bottom: 20px; text-align: left; margin-left: 150px; margin-right: 150px">';
            echo '<div class="entry-details">';
            echo '<p style="font-size: 18px; font-weight: bold;">Entry Date & Time: ' . $row['date_time'] . '</p>';
            echo '<p style="font-size: 18px; font-weight: bold;">Mobile Number: ' . $row['mobileNumber'] . '</p>';
            echo '<p style="font-size: 18px; font-weight: bold;">Full Name: ' . $fullName . '</p>'; // Use the decrypted doctorName
            echo '<p style="font-size: 18px; font-weight: bold;">Previous Observations: ' . $previousObservations . '</p>'; // Use the decrypted hospitalName
            echo '<p style="font-size: 18px; font-weight: bold;">Prescription: ' . $prescription . '</p>'; // Use the decrypted prescription
            echo '</div>';
            echo '</div>';
            $index = ($index + 1) % count($borderColors);
        }
    } else {
        echo "No entries found for this search.";
    }

    $stmt_entries->close();
} else {
    echo "Patient not found for emailPhoneSignup: " . $emailPhoneSignup;
}

function caesarCipherDecrypt($text, $shift)
{
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


    
    <!-- Add a "Back" button -->
    <form method="get" action="doctormain.html">
        <input type="submit" value="Back">
    </form>

</body>
</html>


