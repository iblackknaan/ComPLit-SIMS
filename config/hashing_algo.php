<?php
include 'config.php'; // Make sure this file contains the PDO connection setup

// Hash the passwords
$password1 = password_hash('Allan123', PASSWORD_DEFAULT);
$password2 = password_hash('Janedoe123', PASSWORD_DEFAULT);

try {
    $sql = "INSERT INTO `parents` (`FirstName`, `LastName`, `Username`, `Password`, `UniqueID`, `Phone`, `Email`, `Address`, `DateOfBirth`, `Gender`, `ProfilePicture`, `RelationshipToStudent`)
VALUES
('Kinene', 'Semakula', 'Allan', :password1, 'P123456', '0772738959', 'the1stallan@gmail.com', '123 Main St', '1980-01-01', 'Male', NULL, 'Father'),
('Jane', 'Doe', 'jane_doe', :password2, 'P654321', '0987654321', 'jane@example.com', '456 Elm St', '1975-05-10', 'Female', NULL, 'Mother')";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':password1', $password1);
    $stmt->bindParam(':password2', $password2);

    $stmt->execute();
    echo "Records inserted successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
