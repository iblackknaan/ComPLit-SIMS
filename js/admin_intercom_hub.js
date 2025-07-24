$(document).ready(function() {
    // Handle recipient type change
    $('#recipient_type_view').change(function() {
        var recipientType = $(this).val();
        if (recipientType === 'individual') {
            $('#individual_student_field').show();
        } else {
            $('#individual_student_field').hide();
            $('#student_id').hide(); // Hide student dropdown when not in individual mode
            $('#student_label').hide(); // Hide student label when not in individual mode
        }
    });

    // Handle class selection change
    $('#class_id').change(function() {
        var className = $(this).val(); // Get the selected class name
        var studentDropdown = $('#student_id');
        studentDropdown.empty(); // Clear previous options
        $('#student_label').hide(); // Hide the student label initially
        studentDropdown.hide(); // Hide the student dropdown initially

        if (className) {
            $('#loading').show(); // Show loading indicator

            $.ajax({
                url: 'admin_intercom_hub_fetch_students.php',
                type: 'POST',
                data: { class_name: className },
                success: function(data) {
                    $('#loading').hide(); // Hide loading indicator
                    try {
                        var jsonData = JSON.parse(data);
                        if (jsonData.error) {
                            alert(jsonData.error); // Show error message if no students found
                        } else if (jsonData.length > 0) {
                            $('#student_label').show(); // Show the student label
                            studentDropdown.show(); // Show the student dropdown
                            studentDropdown.empty(); // Clear any previous options
                            $.each(jsonData, function(index, student) {
                                studentDropdown.append(
                                    $('<option></option>').val(student.StudentID).text(student.student_name)
                                );
                            });
                        }
                    } catch (e) {
                        console.error('Failed to parse JSON response:', e);
                        alert('Failed to fetch students.');
                    }
                },
                error: function(xhr, status, error) {
                    $('#loading').hide(); // Hide loading indicator
                    console.error('AJAX Error:', status, error);
                    alert('Failed to fetch students.'); // Handle error
                }
            });
        } else {
            $('#student_id').hide(); // Hide student dropdown if no class selected
            $('#student_label').hide(); // Hide student label if no class selected
        }
    });

    // Dynamically display students list with checkboxes when class is selected
    $('#class-dropdown').change(function() {
        var className = $(this).val(); // Get the selected class name
        $.ajax({
            url: 'admin_intercom_hub_fetch_students.php',
            type: 'POST',
            data: { class_name: className },
            success: function(data) {
                try {
                    var students = JSON.parse(data);
                    var studentsList = $('#students-list');
                    studentsList.html(''); // Clear previous list
                    if (students.length > 0) {
                        students.forEach(function(student) {
                            var checkbox = '<input type="checkbox" name="student_ids[]" value="' + student.StudentID + '">';
                            var studentEntry = $('<div></div>').append(
                                $('<label></label>').html(checkbox + ' ' + student.student_name)
                            );
                            studentsList.append(studentEntry);
                        });
                    } else {
                        studentsList.append('<p>No students found for the selected class.</p>');
                    }
                } catch (e) {
                    console.error('Failed to parse JSON response:', e);
                    alert('Failed to fetch students.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('Failed to fetch students.'); // Handle error
            }
        });
    });

    // Handle select all checkbox
    $('#select_all').click(function() {
        $('input[name="marked_messages[]"]').prop('checked', this.checked);
    });
});
