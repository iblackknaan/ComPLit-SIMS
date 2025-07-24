function fetchSubjects(classID) {
    fetch('fetch_attendance_subjects.php?classID=' + classID)
        .then(response => response.json())
        .then(data => {
            const subjectSelect = document.getElementById('subjectID');
            subjectSelect.innerHTML = '<option value="">Select Subject</option>'; // Add default option

            if (Array.isArray(data) && data.length > 0) {
                data.forEach(subject => {
                    const option = document.createElement('option');
                    option.value = subject.SubjectID;
                    option.text = subject.SubjectName;
                    subjectSelect.appendChild(option);
                });
                subjectSelect.disabled = false; // Enable select if data is present
            } else {
                subjectSelect.disabled = true; // Disable select if no data
                subjectSelect.innerHTML += '<option value="">No subjects available</option>';
            }

            // Check if all fields are selected after fetching subjects
            checkAllFieldsSelected();
        })
        .catch(error => {
            console.error('Error fetching subjects:', error);
            const subjectSelect = document.getElementById('subjectID');
            subjectSelect.disabled = true;
            subjectSelect.innerHTML = '<option value="">Error fetching subjects</option>';
        });
}

function fetchStudents() {
    const classID = document.getElementById('classID').value;
    const subjectID = document.getElementById('subjectID').value;
    const termID = document.getElementById('hiddenTermID').value;
    const academicYear = document.getElementById('academicYear').value;

    // Check if all fields are selected before fetching students
    if (!checkAllFieldsSelected()) {
        return;
    }

    fetch('fetch_attendance_students.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            'classID': classID,
            'subjectID': subjectID,
            'termID': termID,
            'academicYear': academicYear
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            document.getElementById('student-list').innerHTML = '<p class="error">' + data.error + '</p>';
            document.getElementById('save-attendance').style.display = 'none';
        } else {
            let html = '';
            let presentCount = 0;
            let absentCount = 0;
            let lateCount = 0;
            let excusedCount = 0;

            if (data.length > 0) {
                html += '<table><thead><tr><th>Student ID</th><th>Student Name</th><th>Present</th><th>Absent</th><th>Late</th><th>Excused</th></tr></thead><tbody>';
                data.forEach(student => {
                    html += `
                        <tr>
                            <td>${student.StudentID}</td>
                            <td>${student.StudentName}</td>
                            <td><input type="radio" name="status[${student.StudentID}]" value="present" checked onchange="updateCount()"></td>
                            <td><input type="radio" name="status[${student.StudentID}]" value="absent" onchange="updateCount()"></td>
                            <td><input type="radio" name="status[${student.StudentID}]" value="late" onchange="updateCount()"></td>
                            <td><input type="radio" name="status[${student.StudentID}]" value="excused" onchange="updateCount()"></td>
                        </tr>
                    `;
                    presentCount++; // Initial count based on the default checked status
                });
                html += '</tbody>';
                html += `
                    <tfoot>
                        <tr>
                            <td colspan="2"><strong>Total</strong></td>
                            <td><span id="presentCount">${presentCount}</span></td>
                            <td><span id="absentCount">${absentCount}</span></td>
                            <td><span id="lateCount">${lateCount}</span></td>
                            <td><span id="excusedCount">${excusedCount}</span></td>
                        </tr>
                    </tfoot>
                `;
                html += '</table>';
                document.getElementById('save-attendance').style.display = 'block';
            } else {
                html += '<p>No students found.</p>';
                document.getElementById('save-attendance').style.display = 'none';
            }
            document.getElementById('student-list').innerHTML = html;
        }
    })
    .catch(error => {
        document.getElementById('student-list').innerHTML = '<p class="error">An error occurred: ' + error.message + '</p>';
        document.getElementById('save-attendance').style.display = 'none';
    });
}

function updateCount() {
    const presentCount = document.querySelectorAll('input[value="present"]:checked').length;
    const absentCount = document.querySelectorAll('input[value="absent"]:checked').length;
    const lateCount = document.querySelectorAll('input[value="late"]:checked').length;
    const excusedCount = document.querySelectorAll('input[value="excused"]:checked').length;

    document.getElementById('presentCount').textContent = presentCount;
    document.getElementById('absentCount').textContent = absentCount;
    document.getElementById('lateCount').textContent = lateCount;
    document.getElementById('excusedCount').textContent = excusedCount;
}

function saveAttendance() {
    const classID = document.getElementById('classID').value;
    const subjectID = document.getElementById('subjectID').value;
    const termID = document.getElementById('hiddenTermID').value;
    const academicYear = document.getElementById('academicYear').value;
    const attendance = {};

    document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
        const studentID = radio.name.match(/\d+/)[0];
        const status = radio.value;
        attendance[studentID] = status;
    });

    fetch('attendance_tracker_saving_logic.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            'classID': classID,
            'subjectID': subjectID,
            'termID': termID,
            'academicYear': academicYear,
            'attendance': JSON.stringify(attendance)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert('Error: ' + data.error);
        } else {
            alert('Success: ' + data.success);
        }
    })
    .catch(error => {
        alert('An error occurred: ' + error.message);
    });
}

function checkAllFieldsSelected() {
    const classID = document.getElementById('classID').value;
    const subjectID = document.getElementById('subjectID').value;
    const termID = document.getElementById('hiddenTermID').value;
    const academicYear = document.getElementById('academicYear').value;
    const studentList = document.getElementById('student-list');
    const saveButton = document.getElementById('save-attendance');

    if (!classID || !subjectID || !termID || !academicYear) {
        studentList.innerHTML = '<p class="error">Please select all fields.</p>';
        saveButton.style.display = 'none';
        return false;
    }

    return true;
}
