
<div class="container">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-12 bg-warning">
            <br>
            <form method="post" action="TeacherRegistrationFormLogic.php" enctype="multipart/form-data">

                <h2 class="text-center">TEACHER FORM</h2>

                <div class="form-group">
                    <label for="FirstName">First Name:</label>
                    <input type="text" class="form-control" id="FirstName" name="FirstName" required>
                </div>
                <div class="form-group">
                    <label for="LastName">Last Name:</label>
                    <input type="text" class="form-control" id="LastName" name="LastName" required>
                </div>
                <div class="form-group">
                    <label for="Username">Username:</label>
                    <input type="text" class="form-control" id="Username" name="Username" required readonly>
                </div>
                <div class="form-group">
                    <label for="Password">Password:</label>
                    <input type="password" class="form-control" id="Password" name="Password" required>
                    <small id="passwordHelpBlock" class="form-text text-muted">
                        Your password must be at least 8 characters long, contain at least one letter, one digit, and one special character.
                    </small>
                </div>
                <div class="form-group">
                    <label for="ConfirmPassword">Confirm Password:</label>
                    <input type="password" class="form-control" id="ConfirmPassword" name="ConfirmPassword" required>
                </div>
                <div class="form-group">
                    <label for="UniqueID">Unique ID:</label>
                    <input type="text" placeholder="This will be Auto Filled" id="UniqueID" name="UniqueID" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="DateOfBirth">Date of Birth:</label>
                    <input type="date" class="form-control" id="DateOfBirth" name="DateOfBirth" required>
                </div>
                <div class="form-group">
                    <label for="Gender">Gender:</label>
                    <select id="Gender" name="Gender" class="form-control" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="Address">Address:</label>
                    <input type="text" class="form-control" id="Address" name="Address" required>
                </div>
                <div class="form-group">
                    <label for="Phone">Phone:</label>
                    <input type="tel" id="Phone" name="Phone" pattern="[0-9]{4}-[0-9]{3}-[0-9]{3}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="Email">Email:</label>
                    <input type="email" class="form-control" id="Email" name="Email" required>
                </div>
                <div class="form-group">
                    <label for="HireDate">Hire Date:</label>
                    <input type="date" id="HireDate" name="HireDate" class="form-control" value="<?php echo date('Y-m-d'); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="SubjectID">Subject ID:</label>
                    <input type="text" id="SubjectID" name="SubjectID" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="Department">Department:</label>
                    <input type="text" class="form-control" id="Department" name="Department" required>
                </div>
                <div class="form-group">
                    <label for="Qualification">Qualification:</label>
                    <input type="text" class="form-control" id="Qualification" name="Qualification" required>
                </div>
                <div class="form-group">
                    <label for="Experience">Experience:</label>
                    <input type="text" class="form-control" id="Experience" name="Experience" required>
                </div>
                <div class="form-group">
                    <label for="ProfilePicture">Profile Picture:</label>
                    <input type="file" id="ProfilePicture" name="ProfilePicture" class="form-control" accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary">Add Teacher</button>
            </form>
            <br><br>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>