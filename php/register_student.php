<?php
    // Start session to store messages
    session_start();
    
    include 'db_connection.php';
    
    // Check database connection
    if (!$conn) {
        $_SESSION['message'] = "Database connection failed: " . mysqli_connect_error();
        header("Location: ../Student Registration Page.html");
        exit;
    }
    
    // Check if form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form data
        $student_id = trim($_POST['studentid'] ?? '');
        $student_name = trim($_POST['studentname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $birth_date = trim($_POST['birthdate'] ?? '');
        $gender = trim($_POST['gender'] ?? '');
        $education_level = trim($_POST['education_level'] ?? '');
        $course = trim($_POST['course'] ?? '');
    
        // Validation patterns
        $studentIdRegex = '/^PA[A-Za-z0-9]{6}$/';
        $nameRegex = '/^[A-Za-z ]+$/';
        $phoneRegex = '/^(?:\+?60|0)1[0-9](?:[-\s]?\d{3,4}){2}$/';
        $emailRegex = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
    
        // Validate required fields and formats
        $errors = [];
        
        if (empty($student_id) || !preg_match($studentIdRegex, $student_id)) {
            $errors[] = "Student ID must be in format PA followed by 6 alphanumeric characters (e.g., PA123ABC)";
        }
        if (empty($student_name) || !preg_match($nameRegex, $student_name)) {
            $errors[] = "Student Name must contain only letters and spaces";
        }
        if (empty($email) || !preg_match($emailRegex, $email)) {
            $errors[] = "Please enter a valid email address";
        }
        if (empty($phone) || !preg_match($phoneRegex, $phone)) {
            $errors[] = "Phone number must be a valid Malaysian number (e.g., +6012-345 6789)";
        }
        if (empty($birth_date)) {
            $errors[] = "Birth date is required";
        }
        if (empty($gender)) {
            $errors[] = "Gender is required";
        }
        if (empty($education_level)) {
            $errors[] = "Education level is required";
        }
        if (empty($course)) {
            $errors[] = "Course is required";
        }
    
        // If there are validation errors
        if (!empty($errors)) {
            $_SESSION['message'] = implode("<br>", $errors);
            header("Location: ../Student Registration Page.html");
            exit;
        }
    
        // Check if student ID already exists
        $check_sql = "SELECT student_id FROM student_profiles WHERE student_id = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $_SESSION['message'] = "Student ID already exists";
            header("Location: ../Student Registration Page.html");
            $stmt->close();
            $conn->close();
            exit;
        }
        $stmt->close();
    
        // Insert data into database
        $sql = "INSERT INTO student_profiles (student_id, student_name, email, phone, birth_date, gender, education_level, course) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $student_id, $student_name, $email, $phone, $birth_date, $gender, $education_level, $course);
    
        if ($stmt->execute()) {
            $_SESSION['message'] = "Student registered successfully!";
            header("Location: ../Admin View.html"); // Redirect to admin page on success
        } else {
            $_SESSION['message'] = "Error registering student: " . $stmt->error;
            header("Location: ../Student Registration Page.html");
        }
    
        $stmt->close();
        $conn->close();
    } else {
        // If someone tries to access the PHP directly
        header("Location: ../Student Registration Page.html");
        exit;
    }
?>