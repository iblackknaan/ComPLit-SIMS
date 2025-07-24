<!-- index.php -->
<?php
// Include database connection
include 'config.php';

// Function to fetch first name from database
function getFirstName($username, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE Username = ?");
    $stmt->execute([$username]);
    return $stmt->fetchColumn(1); // Fetch the first name (column index 1)
}

// Check if a username is provided in the request
$placeholder = ''; // Default placeholder value
if (isset($_GET['username'])) {
    $username = $_GET['username'];
    $FirstName = getFirstName($username, $pdo);
    $placeholder = $FirstName ? $FirstName : ''; // Update placeholder value if first name is found
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Username Form</title>
</head>
<body>
    <?php if (!isset($_GET['username'])) { ?>
        <h2>Enter Username</h2>
    <?php } ?>
    <input type="text" id="username" name="username" required placeholder="<?php echo $placeholder; ?>">

    <p id="FirstName"></p> <!-- Add a <p> element to display the first name -->

<script>
    const usernameInput = document.getElementById('username');
    const firstNameElement = document.getElementById('FirstName');

    usernameInput.addEventListener('keyup', function(event) {
        const username = usernameInput.value.trim();
        if (username !== '') {
            // Make an AJAX request to fetch the corresponding first name from the database
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'student_termly_registration.php?username=' + username, true); // Use the same file for AJAX request
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const firstName = xhr.responseText;
                    firstNameElement.innerHTML = firstName;
                } else {
                    console.error('Request failed. Status: ' + xhr.status);
                }
            };
            xhr.send();
        } else {
            firstNameElement.innerHTML = ''; // Clear the first name element if the input field is empty
        }
    });
</script>
</body>
</html>