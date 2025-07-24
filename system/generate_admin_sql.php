<?php
// Configuration
$adminUsername = 'knaan';
$adminPassword = '1-bl4ckKn44n'; // Replace with a secure password
$adminFirstName = 'Migadde';
$adminLastName = 'Canan';
$adminPhone = '0784845785';
$adminEmail = 'knaan88.mc@gmail.com';
$adminRole = 'System Administrator';
$adminUniqueID = uniqid('admin_knaan', true); // Generate a unique identifier

// Hash the password
$hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);

// Display the SQL query
$sql = "INSERT INTO admins (FirstName, LastName, Username, Password, UniqueID, Phone, Email, Role) VALUES 
('$adminFirstName', '$adminLastName', '$adminUsername', '$hashedPassword', '$adminUniqueID', '$adminPhone', '$adminEmail', '$adminRole');";

echo $sql;
?>
