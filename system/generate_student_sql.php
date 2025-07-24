<?php
// Database connection setup
require_once 'config.php'; // Adjust the path to your config file

// Array with student data
$students = [
    [
        'StudentID' => 1,
        'FirstName' => 'Namakula',
        'LastName' => 'Aisha',
        'Username' => 'Asha',
        'Password' => 'password1', // Plain text password
        'UniqueID' => 'U1234567',
        'DateOfBirth' => '2002-05-15',
        'Gender' => 'Female',
        'Address' => '123 Aisha St, Kampala',
        'Phone' => '0701234567',
        'Email' => 'aisha@example.com',
        'EnrollmentDate' => '2020-01-10',
        'ClassID' => 2
    ],
    [
        'StudentID' => 2,
        'FirstName' => 'Gaju',
        'LastName' => 'Jolie',
        'Username' => 'jojo',
        'Password' => 'password2', // Plain text password
        'UniqueID' => 'U7654321',
        'DateOfBirth' => '2001-08-23',
        'Gender' => 'Female',
        'Address' => '456 Jolie Ave, Kampala',
        'Phone' => '0707654321',
        'Email' => 'jolie@example.com',
        'EnrollmentDate' => '2019-08-20',
        'ClassID' => 3
    ]
];

try {
    // Start transaction
    $pdo->beginTransaction();

    // Prepare the SQL statement
    $sql = "INSERT INTO `students` 
            (`StudentID`, `FirstName`, `LastName`, `Username`, `Password`, `UniqueID`, `DateOfBirth`, `Gender`, `Address`, `Phone`, `Email`, `EnrollmentDate`, `ClassID`) 
            VALUES (:StudentID, :FirstName, :LastName, :Username, :Password, :UniqueID, :DateOfBirth, :Gender, :Address, :Phone, :Email, :EnrollmentDate, :ClassID)";

    $stmt = $pdo->prepare($sql);

    // Insert each student
    foreach ($students as $student) {
        // Hash the password
        $hashedPassword = password_hash($student['Password'], PASSWORD_DEFAULT);

        // Bind the parameters
        $stmt->bindParam(':StudentID', $student['StudentID']);
        $stmt->bindParam(':FirstName', $student['FirstName']);
        $stmt->bindParam(':LastName', $student['LastName']);
        $stmt->bindParam(':Username', $student['Username']);
        $stmt->bindParam(':Password', $hashedPassword);
        $stmt->bindParam(':UniqueID', $student['UniqueID']);
        $stmt->bindParam(':DateOfBirth', $student['DateOfBirth']);
        $stmt->bindParam(':Gender', $student['Gender']);
        $stmt->bindParam(':Address', $student['Address']);
        $stmt->bindParam(':Phone', $student['Phone']);
        $stmt->bindParam(':Email', $student['Email']);
        $stmt->bindParam(':EnrollmentDate', $student['EnrollmentDate']);
        $stmt->bindParam(':ClassID', $student['ClassID']);

        // Execute the statement
        $stmt->execute();
    }

    // Commit transaction
    $pdo->commit();

    echo "Students inserted successfully!";
} catch (Exception $e) {
    // Rollback transaction if something goes wrong
    $pdo->rollBack();
    echo "Failed to insert students: " . $e->getMessage();
}
?>
