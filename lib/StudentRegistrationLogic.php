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
    $sql = "SELECT uniqueid FROM students WHERE uniqueid LIKE :pattern ORDER BY uniqueid DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $pattern = 'KISA/STU/' . $year . '/%';
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
    return "KISA/STU/$year/$new_number";
}

// Function to generate a unique username
function generate_unique_username($pdo, $surname, $firstname) {
    $year = date('y'); // Get the last two digits of the current year
    $base_username = strtolower(substr($surname, 0, 3) . substr($firstname, 0, 2)) . $year;
    $username = $base_username;

    $count = 1;
    while (true) {
        $sql = "SELECT COUNT(*) FROM students WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username' => $username]);
        $exists = $stmt->fetchColumn();

        if (!$exists) {
            return $username;
        }

        $username = $base_username . $count;
        $count++;
    }
}

// Function to get current academic year
function get_current_academic_year() {
    return date('Y');
}

// Function to get current term
function get_current_term() {
    global $pdo; // Access the global $pdo variable

    // Prepare and execute the SQL statement
    $sql = "SELECT TermName FROM term WHERE CURRENT_DATE BETWEEN StartDate AND EndDate";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $currentTerm = $stmt->fetchColumn(); // Fetch the current term name

    // If no term is found, determine the term based on the current month
    if ($currentTerm === false) {
        $currentMonth = date('n');
        if ($currentMonth >= 2 && $currentMonth <= 4) {
            $currentTerm = '1st Term';
        } elseif ($currentMonth >= 5 && $currentMonth <= 8) {
            $currentTerm = '2nd Term';
        } else {
            $currentTerm = '3rd Term';
        }
    }

    return $currentTerm;
}


// Improved password validation
function validate_password($password) {
    $errors = [];

    // Minimum length check
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }

    // Check for at least one letter
    if (!preg_match('/[A-Za-z]/', $password)) {
        $errors[] = 'Password must contain at least one letter.';
    }

    // Check for at least one digit
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain at least one digit.';
    }

    // Check for at least one special character
    if (!preg_match('/[\W_]/', $password)) {
        $errors[] = 'Password must contain at least one special character (e.g., @, #, $, %, &).';
    }

    // Check for at least one uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must contain at least one uppercase letter.';
    }

    return $errors;
}

// Function to get current class ID from joining class
function get_current_class_id($pdo, $joiningclass) {
    $sql = "SELECT id FROM classes WHERE name = :joiningclass";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['joiningclass' => $joiningclass]);
    return $stmt->fetchColumn();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if required fields are set
    $required_fields = ["studentfirstname", "studentlastname", "studentpassword", "studentdob", "studentgender", "studentaddress", "studentphone", "studentemail", "studentjoiningclass", "currentclassid"];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
            exit();
        }
    }

    // Sanitize input data
    $firstname = sanitize_input($_POST["studentfirstname"]);
    $lastname = sanitize_input($_POST["studentlastname"]);
    $password = sanitize_input($_POST["studentpassword"]);
    $dob = sanitize_input($_POST["studentdob"], 100); // Allow only dates from 100 years ago
    $gender = sanitize_input($_POST["studentgender"]);
    $address = sanitize_input($_POST["studentaddress"]);
    $phone = sanitize_input($_POST["studentphone"]);
    $email = sanitize_input($_POST["studentemail"]);
    $enrollmentdate = date('Y-m-d'); // Auto-fill with current date
    $joiningclass = sanitize_input($_POST["studentjoiningclass"]);
    $currentclass = $joiningclass; // Assuming current class is the same as joining class initially
    $currentclassid = sanitize_input($_POST["currentclassid"]);
    $currentacademicyear = get_current_academic_year();
    $currentterm = get_current_term($pdo); // Fetch current term

    // Generate a unique username
    $username = generate_unique_username($pdo, $lastname, $firstname);

    // Validate password
    $errors = validate_password($password);
    if (!empty($errors)) {
        $errorMessage = implode("\n", $errors);
        echo "<script>alert('$errorMessage'); window.history.back();</script>";
        exit();
    }

    // Hash the password
    $hashedpassword = password_hash($password, PASSWORD_BCRYPT);

    // Generate unique ID
    $uniqueid = generate_unique_id($pdo);

    // Handle profile picture upload
    $profilepicture = '';
    $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (isset($_FILES['student_ProfilePicture']) && $_FILES['student_ProfilePicture']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['student_ProfilePicture']['tmp_name'];
        $fileName = $_FILES['student_ProfilePicture']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDir = 'uploads/Students/';
            
            // Ensure the upload directory exists and is writable
            if (!file_exists($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }
            
            $dest_path = $uploadFileDir . str_replace("/", "_", $uniqueid) . '.' . $fileExtension;  // Change / to _ in uniqueid for filename

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $profilepicture = $dest_path;
            } else {
                echo "<script>alert('There was an error moving the uploaded file to $dest_path.'); window.history.back();</script>";
                exit();
            }
        } else {
            echo "<script>alert('Upload failed. Only JPG, JPEG, PNG, and GIF files are allowed.'); window.history.back();</script>";
            exit();
        }
    }

    // Prepare and execute the SQL statement
    $sql = "INSERT INTO students (firstname, lastname, username, password, uniqueid, DateOfBirth, gender, address, phone, email, EnrollmentDate, JoiningClass, CurrentClass, CurrentClassID, CurrentAcademicYear, CurrentTerm, profilepicture) 
            VALUES (:firstname, :lastname, :username, :password, :uniqueid, :dob, :gender, :address, :phone, :email, :enrollmentdate, :joiningclass, :currentclass, :currentclassid, :currentacademicyear, :currentterm, :profilepicture)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        'firstname' => $firstname,
        'lastname' => $lastname,
        'username' => $username,
        'password' => $hashedpassword,
        'uniqueid' => $uniqueid,
        'dob' => $dob,
        'gender' => $gender,
        'address' => $address,
        'phone' => $phone,
        'email' => $email,
        'enrollmentdate' => $enrollmentdate,
        'joiningclass' => $joiningclass,
        'currentclass' => $joiningclass, // Assuming current class is the same as joining class initially
        'currentclassid' => $currentclassid,
        'currentacademicyear' => $currentacademicyear,
        'currentterm' => $currentterm,
        'profilepicture' => $profilepicture
    ]);

    if ($result) {
        echo "<script>alert('Student registered successfully.'); window.location.href='students_list.php';</script>";
    } else {
        echo "<script>alert('Registration failed. Please try again.'); window.history.back();</script>";
    }
}
?>
