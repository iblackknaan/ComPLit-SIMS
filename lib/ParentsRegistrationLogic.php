 <?php
require 'config.php'; // Ensure this path is correct

// Function to sanitize input data
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Function to generate unique ID
function generate_unique_id($pdo, $usertype) {
    $currentYear = date('Y'); // Get the last two digits of the current year
    $prefix = "KISA/{$currentYear}/";
    
    if ($usertype == "student") {
        $table = "students";
    } elseif ($usertype == "teacher") {
        $table = "teachers";
    } elseif ($usertype == "parent") {
        $table = "parents";
    } else {
        return null;
    }
    
    // Get the highest numeric part of the uniqueID for the current year
    $sql = "SELECT MAX(CAST(SUBSTRING(uniqueid, LENGTH('{$prefix}') + 1) AS UNSIGNED)) AS max_id FROM {$table} WHERE uniqueid LIKE :prefix_pattern";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['prefix_pattern' => "{$prefix}%"]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $nextId = ($result && $result['max_id']) ? $result['max_id'] + 1 : 1;
    return $prefix . str_pad($nextId, 4, '0', STR_PAD_LEFT); // Pad the number with leading zeros
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get common form data
    $firstname = sanitize_input($_POST["firstname"] ?? '');
    $lastname = sanitize_input($_POST["lastname"] ?? '');
    $usertype = sanitize_input($_POST["usertype"] ?? '');

    if (empty($firstname) || empty($lastname) || empty($usertype)) {
    echo "Please fill in all required fields.";
    exit();}


// Handle profile picture upload
    $profile_picture = '';
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileSize = $_FILES['profile_picture']['size'];
        $fileType = $_FILES['profile_picture']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Check file size (e.g., limit to 2MB)
        if ($fileSize > 2 * 1024 * 1024) {
            echo "File size exceeds the maximum limit of 2MB.";
            exit();
        }

        // Check file type (only allow JPEG, PNG, GIF)
        $allowedfileExtensions = array('jpg', 'jpeg', 'png', 'gif');
        if (!in_array($fileExtension, $allowedfileExtensions)) {
            echo "Upload failed. Only JPG, JPEG, PNG, and GIF files are allowed.";
            exit();
        }

        // Create a unique name for the file before saving it
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

        // Directory to save the uploaded files
        $uploadFileDir = './uploads/';
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $profile_picture = $newFileName;
        } else {
            echo "There was an error moving the uploaded file.";
            exit();
        }
    }


    try {
        if ($usertype == "student") {
            // Existing field values
            $firstname = sanitize_input($_POST["firstname"] ?? '');
            $lastname = sanitize_input($_POST["lastname"] ?? '');            
            $username = sanitize_input($_POST["student_username"] ?? '');
            $password = password_hash(sanitize_input($_POST["student_password"] ?? ''), PASSWORD_BCRYPT);
            $uniqueid = generate_unique_id($pdo, $usertype); // Generate unique ID
            $dob = sanitize_input($_POST["student_dob"] ?? '');
            $gender = sanitize_input($_POST["student_gender"] ?? '');
            $address = sanitize_input($_POST["student_address"] ?? '');
            $phone = sanitize_input($_POST["student_phone"] ?? '');
            $email = sanitize_input($_POST["student_email"] ?? '');
            $enrollment_date = date('Y-m-d'); // Auto-fill with current date
            $class_id = sanitize_input($_POST["student_classid"] ?? '');
            $profilepicture = sanitize_input($_POST["student_ProfilePicture"] ?? ''); 


            if (empty($firstname) || empty($lastname)) {
             echo "Please fill in all required fields.";
                exit();} 


            // Validate password
            if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
                echo "Password must be at least 8 characters long and contain at least one letter and one digit.";
                exit();
            }


            // Prepare and execute the SQL statement
            $sql = "INSERT INTO students (firstname, lastname, username, password, uniqueid, DateOfBirth, gender, address, phone, email, EnrollmentDate, ClassID, profilepicture)
                    VALUES (:firstname, :lastname, :username, :password, :uniqueid, :dob, :gender, :address, :phone, :email, :enrollment_date, :class_id, :profilepicture)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'firstname' => $firstname,
                'lastname' => $lastname,
                'username' => $username,
                'password' => $password,
                'uniqueid' => $uniqueid,
                'dob' => $dob,
                'gender' => $gender,
                'address' => $address,
                'phone' => $phone,
                'email' => $email,
                'enrollment_date' => $enrollment_date,
                'class_id' => $class_id,
                'profilepicture' => $profilepicture
            ]);
        } else {
            echo "Invalid user type.";
            exit();
        }

        echo "New record created successfully";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>












 elseif ($usertype == "parent") {
            $username = sanitize_input($_POST["parent_username"] ?? '');
            $password = password_hash(sanitize_input($_POST["parent_password"] ?? ''), PASSWORD_BCRYPT);
            $uniqueid = generate_unique_id($pdo, $usertype); // Generate unique ID
            $phone = sanitize_input($_POST["parent_phone"] ?? '');
            $email = sanitize_input($_POST["parent_email"] ?? '');
            $address = sanitize_input($_POST["parent_address"] ?? '');
            $dob = sanitize_input($_POST["parent_dob"] ?? '');  
            $gender = sanitize_input($_POST["parent_gender"] ?? '');            
            $relationship = sanitize_input($_POST["parent_RelationshipToStudent"] ?? '');
            $child_id = sanitize_input($_POST["parent_StudentID"] ?? '');
            $profilepicture = sanitize_input($_POST["parent_ProfilePicture"] ?? ''); 

            // Validate password
            if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
                echo "Password must be at least 8 characters long and contain at least one letter and one digit.";
                exit();
            }


            $sql = "INSERT INTO parents (firstname, lastname, username, password, uniqueid, phone, email, address, DateOfBirth, gender, RelationshipToStudent, studentID, profilepicture)
                    VALUES (:firstname, :lastname, :username, :password, :uniqueid, :phone, :email, :address, :dob, :gender,  :relationship, :child_id, :profilepicture)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'firstname' => $firstname,
                'lastname' => $lastname,
                'username' => $username,
                'password' => $password,
                'uniqueid' => $uniqueid,
                'phone' => $phone,
                'email' => $email,                  
                'address' => $address,
                'dob' => $dob,
                'gender' => $gender,
                'relationship' => $relationship,              
                'child_id' => $child_id,
                'profilepicture' => $profilepicture
            ]);
        } 