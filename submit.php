<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$output = null; // Initialize $output to avoid undefined variable warnings

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect data from form
    $age = $_POST['age'];
    $sex = $_POST['sex'];
    $cp = $_POST['cp'];
    $trestbps = $_POST['trestbps'];
    $chol = $_POST['chol'];
    $fbs = $_POST['fbs'];
    $restecg = $_POST['restecg'];
    $thalach = $_POST['thalach'];
    $exang = $_POST['exang'];
    $oldpeak = $_POST['oldpeak'];
    $slope = $_POST['slope'];
    $ca = $_POST['ca'];
    $thal = $_POST['thal'];

    // Save data to the database
    $conn = new mysqli("localhost", "root", "", "heart_disease");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO predictions (age, sex, cp, trestbps, chol, fbs, restecg, thalach, exang, oldpeak, slope, ca, thal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiiiiiiiiiii", $age, $sex, $cp, $trestbps, $chol, $fbs, $restecg, $thalach, $exang, $oldpeak, $slope, $ca, $thal);
    $stmt->execute();
    $stmt->close();

    // Call Python script for prediction
    $command = escapeshellcmd("python predict.py $age $sex $cp $trestbps $chol $fbs $restecg $thalach $exang $oldpeak $slope $ca $thal");
    $output = shell_exec($command);


    // Trim the output and check for empty result
    $output = trim($output);
    
    if (!empty($output)) {
        // Decode the JSON output
        $output = json_decode($output, true);

        // Check for JSON decoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "JSON Error: " . json_last_error_msg();
        }
    } else {
        echo "No output returned from the Python script.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Heart Disease Prediction Result</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            color: #333;
        }
        .result {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .prediction {
            font-size: 1.5em;
            font-weight: bold;
            color: #4CAF50;
        }
        .explanation {
            font-size: 1em;
            color: #555;
        }
    </style>
</head>
<body>
    <h1>Heart Disease Prediction</h1>

    <?php if (isset($output) && is_array($output)): ?>
        <div class="result">
            <h2>Prediction Results:</h2>
            <?php foreach ($output as $model => $prediction): ?>
                <p class="prediction">
                    <i class="fas <?= $prediction == 1 ? 'fa-heart' : 'fa-heart-broken' ?>"></i>
                    <?= $model ?> Prediction: <?= $prediction == 1 ? 'Positive' : 'Negative' ?>
                </p>
                <p class="explanation">
                    <?= $prediction == 1 ? 'The model predicts that the patient has a high risk of heart disease.' : 'The model predicts that the patient does not have heart disease.' ?>
                </p>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Error decoding prediction output.</p>
    <?php endif; ?>
</body>
</html>
