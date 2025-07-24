<div class="container">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-12 bg-danger">
		<br>
		<form method="post" action="ParentsRegistrationLogic.php">

        		<h2 class="text-center">PARENT FORM</h2>

	        	<div class="form-group">
		            <label for="parent_firstname">First Name:</label>
		            <input type="text" class="form-control" id="parent_firstname" name="parent_firstname" required>
		        </div>
	       		<div class="form-group">
	            <label for="parent_lastname">Last Name:</label>
	            <input type="text" class="form-control" id="parent_lastname" name="parent_lastname" required>
	        	</div>
                <div class="form-group">
                    <label for="parent_username">Username:</label>
                    <input type="text" class="form-control" id="parent_username" name="parent_username">
                </div>
                <div class="form-group">
                    <label for="parent_password">Password:</label>
                    <input type="password" class="form-control" id="parent_password" name="parent_password">
                    <small id="passwordHelpBlock" class="form-text text-muted">
                        Your password must be at least 8 characters long, contain at least one letter, one digit, and one special character.
                    </small>
                </div>
                <div class="form-group">
                    <label for="parent_confirm_password">Confirm Password:</label>
                    <input type="password" class="form-control" id="parent_confirm_password" name="parent_confirm_password" required>
                </div>
                <div class="form-group">
                    <label for="parent_uniqueid">Unique ID:</label>
                    <input type="text" placeholder="This will be Auto Filled" id="parent_uniqueid" name="parent_uniqueid" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="parent_phone">Phone:</label>
                    <input type="tel" id="parent_phone" name="parent_phone" pattern="[0-9]{4}-[0-9]{3}-[0-9]{3}" class="form-control">
                </div>
                <div class="form-group">
                    <label for="parent_email">Email:</label>
                    <input type="email" id="parent_email" name="parent_email" class="form-control">
                </div>
                <div class="form-group">
                    <label for="parent_address">Address:</label>
                    <input type="text" id="parent_address" name="parent_address" class="form-control">
                </div>
                <div class="form-group">
                    <label for="teacher_dob">Date of Birth:</label>
                    <input type="date" class="form-control" id="teacher_dob" name="teacher_dob">
                </div>
                <div class="form-group">
                    <label for="parent_gender">Gender:</label>
                    <select id="parent_gender" name="parent_gender" class="form-control">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="parent_RelationshipToStudent">Relationship To Student:</label>
                    <input type="text" id="parent_RelationshipToStudent" name="parent_RelationshipToStudent" class="form-control">
                </div>
                <div class="form-group">
                    <label for="parent_StudentID">StudentID:</label>
                    <select id="parent_StudentID" name="parent_StudentID" class="form-control">
                        <?php
                        // Fetch student data from the database
                        $sql = "SELECT StudentID, CONCAT(FirstName, ' ', LastName) AS StudentName FROM students";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute();
                        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // Loop through the students and populate the select options
                        foreach ($students as $student) {
                            $studentID = htmlspecialchars($student['StudentID']);
                            $studentName = htmlspecialchars($student['StudentName']);
                            echo "<option value='$studentID'>$studentName</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="parent_ProfilePicture">Profile Picture:</label>
                    <input type="file" id="parent_ProfilePicture" name="parent_ProfilePicture" class="form-control" accept="image/*">
                </div>


		<button type="submit" class="btn btn-primary">Add Student</button>
        </form>	
		<br><br>
	</div>
</div>
</div>