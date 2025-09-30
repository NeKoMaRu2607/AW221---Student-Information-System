document.addEventListener('DOMContentLoaded', () => {
    let profile = document.querySelector('.header .flex .profile');
    let sideBar = document.querySelector('.side-bar');
    let body = document.querySelector('body');

    document.querySelector('#user-btn').onclick = () => {
        profile.classList.toggle('active');
    }

    window.onscroll = () => {
        profile.classList.remove('active');
        if (window.innerWidth < 1200) {
            sideBar.classList.remove('active');
            body.classList.remove('active');
        }
    }

    document.querySelector('#menu-btn').onclick = () => {
        sideBar.classList.toggle('active');
        body.classList.toggle('active');
    }

    document.querySelector('.side-bar .close-side-bar').onclick = () => {
        sideBar.classList.remove('active');
        body.classList.remove('active');
    }

    function toggleTableVisibility(sectionId, buttonClass) {
        const buttons = document.querySelectorAll(`#${sectionId} .${buttonClass}`);
        const tables = document.querySelectorAll(`#${sectionId} .class-table`);

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                const targetClass = button.dataset.class;
                buttons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                tables.forEach(table => table.style.display = 'none');
                const targetTable = document.getElementById(targetClass);
                if (targetTable) {
                    targetTable.style.display = 'block';
                }
            });
        });
    }

    toggleTableVisibility('intake-results', 'class-btn');
    toggleTableVisibility('student-profile', 'class-btn');

    loadProfileStudents();
    loadResultRecords();

    // Profile event listeners
    document.getElementById('profilesearchButton').addEventListener('click', searchProfileStudents);
    document.getElementById('profileresetSearch').addEventListener('click', resetProfileSearch);
    document.getElementById('profilesearchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchProfileStudents();
        }
    });

    // Result event listeners
    document.getElementById('resultsearchButton').addEventListener('click', searchResultRecords);
    document.getElementById('resultresetSearch').addEventListener('click', resetResultSearch);
    document.getElementById('resultsearchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchResultRecords();
        }
    });
});

// Profile Functions
function loadProfileStudents(id = null) {
    document.getElementById('profileTableBody').innerHTML = '<tr><td colspan="6">Loading...</td></tr>';

    let url = 'http://localhost/AW221/Final%20Project/php/student_profile.php';
    if (id) {
        url += '?id=' + encodeURIComponent(id);
    }

    fetch(url)
        .then(response => {
            console.log('Profile response status:', response.status);
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`Network response was not ok: ${response.status} - ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Profile data:', data);
            const tableBody = document.getElementById('profileTableBody');
            const noResults = document.getElementById('profilenoResults');

            if (data.status === 'success' && data.data.length > 0) {
                let html = '';
                data.data.forEach(student => {
                    html += `<tr class="student-row" data-id="${student.id}">
                        <td>${student.id}</td>
                        <td>${student.student_name}</td>
                        <td>${student.email}</td>
                        <td>${student.phone}</td>
                        <td>${student.age}</td>
                        <td><button class="delete-btn" data-id="${student.id}">Delete</button></td>
                    </tr>`;
                });
                tableBody.innerHTML = html;
                noResults.style.display = 'none';

                document.querySelectorAll('#profileTableBody .delete-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const studentId = this.getAttribute('data-id');
                        if (confirm(`Are you sure you want to delete student with ID ${studentId}?`)) {
                            deleteProfileStudent(studentId);
                        }
                    });
                });
            } else {
                tableBody.innerHTML = '';
                noResults.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error fetching student data:', error);
            document.getElementById('profileTableBody').innerHTML = 
                '<tr><td colspan="6">Error loading data. Please try again.</td></tr>';
        });
}

function deleteProfileStudent(studentId) {
    fetch('http://localhost/AW221/Final%20Project/php/student_profile.php', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: studentId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Student deleted successfully');
            loadProfileStudents();
        } else {
            alert('Error deleting student: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the student');
    });
}

function searchProfileStudents() {
    console.log('Searching profile students');
    const studentId = document.getElementById('profilesearchInput').value.trim();
    if (studentId) {
        loadProfileStudents(studentId);
        document.getElementById('profileresetSearch').style.display = 'block';
    } else {
        resetProfileSearch();
    }
}

function resetProfileSearch() {
    document.getElementById('profilesearchInput').value = '';
    loadProfileStudents();
    document.getElementById('profileresetSearch').style.display = 'none';
    document.getElementById('profilenoResults').style.display = 'none';
}

// Result Functions
function loadResultRecords(id = null) {
    document.getElementById('resultTableBody').innerHTML = '<tr><td colspan="6">Loading...</td></tr>';

    let url = 'http://localhost/AW221/Final%20Project/php/result_records.php';
    if (id) {
        url += '?student_id=' + encodeURIComponent(id);
    }

    fetch(url)
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`Network response was not ok: ${response.status} - ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            const tableBody = document.getElementById('resultTableBody');
            const noResults = document.getElementById('resultnoResults');

            if (data.status === 'success' && data.data.length > 0) {
                let html = '';
                data.data.forEach(result => {
                    html += `<tr class="result-row" data-id="${result.student_id}">
                        <td>${result.student_id}</td>
                        <td>${result.course}</td>
                        <td>${result.gpa}</td>
                        <td>${result.intake_year}</td>
                        <td>${result.status}</td>
                        <td><button class="delete-btn" data-id="${result.student_id}">Delete</button></td>
                    </tr>`;
                });
                tableBody.innerHTML = html;
                noResults.style.display = 'none';

                document.querySelectorAll('#resultTableBody .delete-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const resultId = this.getAttribute('data-id');
                        if (confirm(`Are you sure you want to delete result for student ID ${resultId}?`)) {
                            deleteResultRecord(resultId);
                        }
                    });
                });
            } else {
                tableBody.innerHTML = '';
                noResults.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error fetching result data:', error);
            document.getElementById('resultTableBody').innerHTML = 
                '<tr><td colspan="6">Error loading data. Please try again.</td></tr>';
        });
}

function deleteResultRecord(resultId) {
    fetch('http://localhost/AW221/Final%20Project/php/result_records.php', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ student_id: resultId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Result deleted successfully');
            loadResultRecords();
        } else {
            alert('Error deleting result: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the result');
    });
}

function searchResultRecords() {
    console.log('Searching result records');
    const resultId = document.getElementById('resultsearchInput').value.trim();
    if (resultId) {
        fetch(`http://localhost/AW221/Final%20Project/php/result_records.php?student_id=${encodeURIComponent(resultId)}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && data.data.length > 0) {
                    const studentName = data.data[0].student_name;
                    alert(`Student ID ${resultId} belongs to ${studentName}`);
                }
                loadResultRecords(resultId);
                document.getElementById('resultresetSearch').style.display = 'block';
            })
            .catch(error => {
                console.error('Error fetching student name:', error);
                loadResultRecords(resultId);
                document.getElementById('resultresetSearch').style.display = 'block';
            });
    } else {
        resetResultSearch();
    }
}

function resetResultSearch() {
    document.getElementById('resultsearchInput').value = '';
    loadResultRecords();
    document.getElementById('resultresetSearch').style.display = 'none';
    document.getElementById('resultnoResults').style.display = 'none';
}