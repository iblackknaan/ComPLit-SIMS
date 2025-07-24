<?php
require 'config.php'; // Ensure this path is correct

// Function to sanitize input data
function sanitize_input($data, $max_year = null) {
    $sanitized_data = htmlspecialchars(stripslashes(trim($data)));
    if ($max_year !== null) {
        $current_year = date('Y');
        $max_allowed_year = date('Y', strtotime("-$max_year years"));
        if (strtotime($sanitized_data) > strtotime($max_allowed_year)) {
            $sanitized_data = $max_allowed_year; // Limit to max allowed year
        }
    }
    return $sanitized_data;
}

// Function to get the last assigned number for the current year
function get_last_number($pdo, $year) {
    $sql = "SELECT UniqueID FROM teachers WHERE UniqueID LIKE :pattern ORDER BY UniqueID DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $pattern = 'KISA/TEA/' . $year . '/%';
    $stmt->execute(['pattern' => $pattern]);
    $last_id = $stmt->fetchColumn();

    if ($last_id) {
        $parts = explode('/', $last_id);
        return intval(end($parts));
    }
    return 0;
}

// Function to generate unique ID
function generate_unique_id($pdo) {
    $year = date('y'); // Get the last two digits of the current year
    $last_number = get_last_number($pdo, $year);
    $new_number = str_pad($last_number + 1, 4, '0', STR_PAD_LEFT);
    return "KISA/TEA/$year/$new_number";
}

// Function to generate username
function generate_username($firstname, $lastname) {
    $year = date('y'); // Get the last two digits of the current year
    $username = strtolower(substr($firstname, 0, 3) . substr($lastname, 0, 2) . $year);
    return $username;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if required fields are set
    $required_fields = ["FirstName", "LastName", "Password", "DateOfBirth", "Gender", "Address", "Phone", "Email", "SubjectID", "Department", "Qualification", "Experience"];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
            exit();
        }
    }

    // Sanitize input data
    $firstname = sanitize_input($_POST["FirstName"]);
    $lastname = sanitize_input($_POST["LastName"]);
    $password = sanitize_input($_POST["Password"]);
    $dob = sanitize_input($_POST["DateOfBirth"], 100); // Allow only dates from 100 years ago
    $gender = sanitize_input($_POST["Gender"]);
    $address = sanitize_input($_POST["Address"]);
    $phone = sanitize_input($_POST["Phone"]);
    $email = sanitize_input($_POST["Email"]);
    $hiredate = date('Y-m-d'); // Auto-fill with current date
    $subjectID = sanitize_input($_POST["SubjectID"]);
    $department = sanitize_input($_POST["Department"]);
    $qualification = sanitize_input($_POST["Qualification"]);
    $experience = sanitize_input($_POST["Experience"]);
    $uniqueid = generate_unique_id($pdo); // Generate unique ID
    $username = generate_username($firstname, $lastname); // Generate username

    // Validate passwords
    if ($_POST["Password"] !== $_POST["ConfirmPassword"]) {
        echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $sql = "SELECT COUNT(*) FROM teachers WHERE Email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    if ($stmt->fetchColumn() > 0) {
        echo "<script>alert('Email already exists. Please use a different email.'); window.history.back();</script>";
        exit();
    }

    // File upload logic
    $target_dir = "uploads/";
    $profile_picture = null;
    if (isset($_FILES["ProfilePicture"]) && $_FILES["ProfilePicture"]["error"] == 0) {
        $file_name = basename($_FILES["ProfilePicture"]["name"]);
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file is an image
        $check = getimagesize($_FILES["ProfilePicture"]["tmp_name"]);
        if ($check === false) {
            echo "<script>alert('File is not an image.'); window.history.back();</script>";
            exit();
        }

        // Check file size (limit to 2MB)
        if ($_FILES["ProfilePicture"]["size"] > 2000000) {
            echo "<script>alert('File is too large. Please upload a file smaller than 2MB.'); window.history.back();</script>";
            exit();
        }

        // Allow certain file formats
        $allowed_formats = ["jpg", "png", "jpeg", "gif"];
        if (!in_array($file_type, $allowed_formats)) {
            echo "<script>alert('Only JPG, JPEG, PNG & GIF files are allowed.'); window.history.back();</script>";
            exit();
        }

        // Upload file
        if (!move_uploaded_file($_FILES["ProfilePicture"]["tmp_name"], $target_file)) {
            echo "<script>alert('Sorry, there was an error uploading your file.'); window.history.back();</script>";
            exit();
        }
        $profile_picture = $target_file;
    }

    // Insert data into the database
    try {
        $sql = "INSERT INTO teachers (FirstName, LastName, Username, Password, UniqueID, DateOfBirth, Gender, Address, Phone, Email, HireDate, SubjectID, Department, Qualification, Experience, ProfilePicture)
                VALUES (:firstname, :lastname, :username, :password, :uniqueid, :dob, :gender, :address, :phone, :email, :hiredate, :subjectID, :department, :qualification, :experience, :profile_picture)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':firstname' => $firstname,
            ':lastname' => $lastname,
            ':username' => $username,
            ':password' => $hashed_password,
            ':uniqueid' => $uniqueid,
            ':dob' => $dob,
            ':gender' => $gender,
            ':address' => $address,
            ':phone' => $phone,
            ':email' => $email,
            ':hiredate' => $hiredate,
            ':subjectID' => $subjectID,
            ':department' => $department,
            ':qualification' => $qualification,
            ':experience' => $experience,
            ':profile_picture' => $profile_picture
        ]);

        echo "<script>alert('Teacher registered successfully!'); window.location.href = 'add_user.php';</script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
