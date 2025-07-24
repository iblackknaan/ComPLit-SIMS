<?php
// Include the database configuration file
require 'config.php';

// Initialize variables
$student_name = $student_email = $term_id = "";
$name_err = $email_err = $term_err = $success_msg = $error_msg = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST["student_name"]))) {
        $name_err = "Please enter your name.";
    } else {
        $student_name = trim($_POST["student_name"]);
    }

    // Validate email
    if (empty(trim($_POST["student_email"]))) {
        $email_err = "Please enter your email.";
    } elseif (!filter_var($_POST["student_email"], FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
    } else {
        $student_email = trim($_POST["student_email"]);
    }

    // Validate term
    if (empty($_POST["term_id"])) {
        $term_err = "Please select a term.";
    } else {
        $term_id = $_POST["term_id"];
    }

    // Check for errors before inserting into the database
    if (empty($name_err) && empty($email_err) && empty($term_err)) {
        try {
            // Prepare an insert statement
            $stmt = $pdo->prepare("INSERT INTO registrations (student_name, student_email, term_id) VALUES (?, ?, ?)");
            $stmt->execute([$student_name, $student_email, $term_id]);
            $success_msg = "Registration successful!";
        } catch (PDOException $e) {
            // Log the error and show a generic error message to the user
            error_log('Database error: ' . $e->getMessage(), 0);
            $error_msg = "An unexpected error occurred. Please try again later.";
        }
    }
}

// Fetch available terms from the database
$terms = [];
try {
    $stmt = $pdo->query("SELECT id, name, start_date, end_date FROM terms");
    while ($row = $stmt->fetch()) {
        $terms[] = $row;
    }
} catch (PDOException $e) {
    // Log the error and show a generic error message to the user
    error_log('Database error: ' . $e->getMessage(), 0);
    $error_msg = "An unexpected error occurred. Please try again later.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termly Registration</title>
</head>
<body>
    <h1>Register for a Term</h1>

    <?php
    if (!empty($success_msg)) {
        echo '<p style="color: green;">' . $success_msg . '</p>';
    }

    if (!empty($error_msg)) {
        echo '<p style="color: red;">' . $error_msg . '</p>';
    }
    ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="student_name">Name:</label>
        <input type="text" id="student_name" name="student_name" value="<?php echo htmlspecialchars($student_name); ?>" required>
        <span style="color: red;"><?php echo $name_err; ?></span>
        <br><br>

        <label for="student_email">Email:</label>
        <input type="email" id="student_email" name="student_email" value="<?php echo htmlspecialchars($student_email); ?>" required>
        <span style="color: red;"><?php echo $email_err; ?></span>
        <br><br>

        <label for="term">Select Term:</label>
        <select id="term" name="term_id" required>
            <option value="">-- Select a Term --</option>
            <?php
            foreach ($terms as $term) {
                echo '<option value="' . htmlspecialchars($term['id']) . '" ' . ($term_id == $term['id'] ? 'selected' : '') . '>'
                     . htmlspecialchars($term['name']) . ' (' . htmlspecialchars($term['start_date']) . ' - ' . htmlspecialchars($term['end_date']) . ')</option>';
            }
            ?>
        </select>
        <span style="color: red;"><?php echo $term_err; ?></span>
        <br><br>

        <input type="submit" value="Register">
    </form>
</body>
</html>
