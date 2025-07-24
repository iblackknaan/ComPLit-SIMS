$(document).ready(function() {
    // Function to clear form fields
    function clearFormFields() {
        $('#StudentID').val('');
        $('#StudentName').text('');
        $('#class').val('');
        $('#CurrentClassID').val('');
        $('#mandatory_subjects').html('');
        $('#elective_subjects').html('');
        $('#optional_subjects').html('');
        $('#other_nursery_areas').html('');
    }

    // Function to handle response data
    function handleResponse(data) {
        console.log('Response Data:', data);

        if (data.error) {
            console.error('Error:', data.error);
            clearFormFields();
        } else {
            // Populate the form fields with the response data
            $('#StudentID').val(data.StudentID || '');
            $('#StudentName').text(data.StudentName || 'No student data');
            $('#class').val(data.CurrentClass || '');
            $('#CurrentClassID').val(data.CurrentClassID || '');
            $('#mandatory_subjects').html(data.mandatorySubjects || '');
            $('#elective_subjects').html(data.electiveSubjects || '');
            $('#optional_subjects').html(data.optionalSubjects || '');
            $('#other_nursery_areas').html(data.otherNurseryAreas || '');

            if (data.SectionName !== 'Nursery Section') {
                $('#other_nursery_areas').hide(); // Hide the section for non-Nursery students
            } else {
                $('#other_nursery_areas').show(); // Show the section for Nursery students
            }
        }
    }

    // Function to perform AJAX GET request
    function fetchStudentData(uniqueID) {
        $.ajax({
            url: 'fetch_student_dynamic_data.php',
            type: 'GET',
            data: { UniqueID: uniqueID },
            dataType: 'json',
            success: handleResponse,
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Failed to fetch data:', textStatus, errorThrown);
                clearFormFields();
            }
        });
    }

    // Event handler for input change
    $('#UniqueID').on('input', function() {
        var uniqueID = $(this).val().trim();

        if (uniqueID) {
            fetchStudentData(uniqueID);
        } else {
            clearFormFields();
        }
    });

    // Event handler for section change
    $('#section').on('change', function() {
        var selectedSection = $(this).val();

        if (selectedSection !== 'Nursery Section') {
            $('#other_nursery_areas').hide();
        } else {
            $('#other_nursery_areas').show();
        }

        // Send AJAX request to update form portions
        $.ajax({
            url: 'update_form.php',
            type: 'POST',
            data: { section: selectedSection },
            dataType: 'json',
            success: function(response) {
                console.log('Form Update Response:', response);
                // Update form portions using the received data
                $('#mandatory_subjects').html(response.mandatorySubjects || '');
                $('#elective_subjects').html(response.electiveSubjects || '');
                $('#optional_subjects').html(response.optionalSubjects || '');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Failed to update form:', textStatus, errorThrown);
            }
        });
    });

    // Event handler for form submission
    $('#registrationForm').on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        // Collect subjects data
        var subjectsData = [];
        $('input[name="subjects[]"]:checked').each(function() {
            subjectsData.push(JSON.parse($(this).val()));
        });
        $('#elective_subjects input:checked').each(function() {
            subjectsData.push(JSON.parse($(this).val()));
        });
        $('#optional_subjects input:checked').each(function() {
            subjectsData.push(JSON.parse($(this).val()));
        });

        // Append subjects data as JSON
        formData.append('subjects', JSON.stringify(subjectsData));

        // Collect other nursery areas of concern
        var otherNurseryAreas = [];
        $('#other_nursery_areas input:checked').each(function() {
            otherNurseryAreas.push($(this).val());
        });

        // Append other nursery areas to formData
        formData.append('otherNurseryAreas', JSON.stringify(otherNurseryAreas));

        // Submit data using AJAX
        $.ajax({
            url: 'student_termly_registration_logic.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                console.log('Form Submission Response:', response);
                if (response.status === 'success') {
                    alert('Registration submitted successfully!');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {    alert('Failed to submit registration: ' + textStatus + ' - ' + errorThrown)
            ;}});});});