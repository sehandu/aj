<?php
header('Content-Type: application/json');
include '../../db/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullName    = $_POST['fullName'] ?? '';
    $email       = $_POST['email'] ?? '';
    $password    = $_POST['password'] ?? '';
    $confirmPass = $_POST['confirmPassword'] ?? '';

    // Backend validation check
    if (empty($fullName) || empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'All required fields must be filled.']);
        exit;
    }

    if ($password !== $confirmPass) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
        exit;
    }

    // Check if email address is already registered using simple query
    $checkQuery = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($checkQuery);

    if ($result && $result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email address is already registered.']);
        exit;
    }

    // Handle Profile Picture upload
    $profilePicPath = "";
    if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . '_' . basename($_FILES['profilePic']['name']);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['profilePic']['tmp_name'], $targetFilePath)) {
            $profilePicPath = $targetFilePath;
        }
    }

    // Encrypt password
    $encryptedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user record into database using simple query
    $sql = "INSERT INTO users (full_name, encripted_password, email, profile_pic, role) 
            VALUES ('$fullName', '$encryptedPassword', '$email', '$profilePicPath', 'student')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success', 'message' => 'Registration successful!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $conn->error]);
    }

    $conn->close();
}
?>
