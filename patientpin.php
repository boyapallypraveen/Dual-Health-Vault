<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .pin-container {
            border: 2px solid blue;
            padding: 20px; /* Increased padding */
            margin: 20px; /* Increased margin */
            display: inline-block;
            text-align: center; /* Center text in the container */
        }

        .pin-container h2 {
            margin-bottom: 10px; /* Add some space between the header and PIN */
        }

        .button-container {
            margin-top: 20px;
        }

        button {
            font-size: 16px;
            padding: 15px; /* Increased padding for buttons */
            margin: 5px;
            background-color: #4CAF50; /* Green color for buttons */
            color: white; /* White text color for buttons */
            border: none;
            border-radius: 5px; /* Add rounded corners */
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049; /* Darker green color on hover */
        }
    </style>
    <title>Generate PIN</title>
</head>
<body>
    <div class="pin-container" id="pinContainer">
        <?php
        session_start(); // Start the session (if not already started)

        $emailPhoneSignup = $_SESSION['emailPhoneSignup'];

        // Connect to the database
        $conn = new mysqli('localhost:3307', 'root', '', 'mysql');
        if ($conn->connect_error) {
            die("Connection Failed: " . $conn->connect_error);
        }

        // Prepare and execute the query to get patient_id
        $query_patient = "SELECT id FROM patient WHERE emailPhoneSignup = ?";
        $stmt_patient = $conn->prepare($query_patient);

        if (!$stmt_patient) {
            die("Query preparation failed: " . $conn->error);
        }

        $stmt_patient->bind_param("s", $emailPhoneSignup);
        $stmt_patient->execute();

        if ($stmt_patient->errno) {
            die("Query execution failed: " . $stmt_patient->error);
        }

        $stmt_patient->bind_result($patient_id);

        // Fetch the result
        $stmt_patient->fetch();

        // Close the statement
        $stmt_patient->close();

        // Check if a patient ID was found
        if (!$patient_id) {
            die("Patient ID not found for the given email/phone signup.");
        }

        // Generate a 5-digit PIN
        $generatedPin = mt_rand(10000, 99999);

        // Append user ID after 5 digits
        $completePin = $generatedPin . $patient_id;

        // Insert the PIN into the "treatment" table for all rows with the specified "patient_id"
        $query_insert_pin = "UPDATE treatment SET pin = ? WHERE p_id = ?";
        $stmt_insert_pin = $conn->prepare($query_insert_pin);

        if (!$stmt_insert_pin) {
            die("Query preparation failed: " . $conn->error);
        }

        $stmt_insert_pin->bind_param("ss", $completePin, $patient_id);
        $stmt_insert_pin->execute();

        if ($stmt_insert_pin->errno) {
            die("Query execution failed: " . $stmt_insert_pin->error);
        }

        // Check if any records were affected
        if ($stmt_insert_pin->affected_rows > 0) {
            // Display the PIN (ensure proper HTML output sanitization)
            echo "<h2>Your PIN:</h2><h1>" . htmlspecialchars($completePin) . "</h1>";
        } else {
            echo "No data records.";
        }

        // Close the statement
        $stmt_insert_pin->close();

        // Close the database connection
        $conn->close();
        ?>
    </div>

    <div class="button-container">
        <button onclick="regeneratePin()">Regenerate</button>
        <button onclick="goBack()">Back</button>
    </div>

    <script>
        function regeneratePin() {
            // Reload the page to regenerate the PIN
            location.reload();
        }

        function goBack() {
            // Navigate to the main page
            window.location.href = 'patientmain.html';
        }
    </script>
</body>
</html>
