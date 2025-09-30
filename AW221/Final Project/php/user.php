<?php
    include 'db_connection.php';

    // Handle Login
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
        $userid = trim($_POST['userid']);
        $password = trim($_POST['password']);
        $usertype = trim($_POST['usertype']);
        $errors = [];

        //$useridRegex = '/^PA[A-Z0-9]{6}$/';

        /*if (empty($userid)) {
            $errors[] = "User ID is required.";
        } elseif (!preg_match($useridRegex, $userid)) {
            $errors[] = "Please check your User ID again!";
        }
        if (empty($password)) {
            $errors[] = "Password is required. If you have forgotten your password, click the link below.";
        }
        if (empty($usertype)) {
            $errors[] = "Please select a user type.";
        }*/

        if (empty($errors)) {
            try {
                $stmt = $conn->prepare("SELECT user_id, password, user_type FROM users_login WHERE user_id = ? AND user_type = ?");
                $stmt->bind_param("ss", $userid, $usertype);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();
                    if ($password === $user['password']) {
                        // Redirect based on user_type
                        $redirectPage = '';
                        switch ($user['user_type']) {
                            case 'student':
                                $redirectPage = '../Student View.html';
                                break;
                            case 'teacher':
                                $redirectPage = '../Teacher View.html';
                                break;
                            case 'admin':
                                $redirectPage = '../Admin View.html';
                                break;
                            case 'alumni':
                                $redirectPage = '../Alumni View.html';
                                break;
                            default:
                                echo "Login successful, but no redirect page defined for this user type.";
                                $redirectPage = null;
                                break;
                        }

                        if ($redirectPage) {
                            echo "Login successful! Redirecting to " . htmlspecialchars($user['user_type']) . " portal...";
                            echo "<script>
                                    setTimeout(() => {
                                        window.location.href = '$redirectPage';
                                    }, 1500);
                                  </script>";
                        }
                    } else {
                        echo "Invalid password.";
                    }
                } else {
                    echo "Invalid User ID or User Type.";
                }
                $stmt->close();
            } catch (Exception $e) {
                echo "Error during login: " . $e->getMessage();
            }
        } else {
            foreach ($errors as $error) {
                echo "<p>$error</p>";
            }
        }
    }

    // Handle Register
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register'])) {
        $userid = isset($_POST['userid']) ? trim($_POST['userid']) : '';
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
        $usertype = isset($_POST['usertype']) ? trim($_POST['usertype']) : '';
        $errors = [];

        $useridRegex = '/^PA[A-Z0-9]{6}$/';
        $usernameRegex = '/^[A-Za-z]+(?:\s[A-Za-z]+)?$/';
        $passwordRegex = '/^[a-zA-Z0-9@#*!_()]{8,12}$/';

        // Validate User ID based on user type
        if (empty($userid)) {
            $errors[] = "User ID is required.";
        } else {
            if ($usertype === 'student' || $usertype === 'alumni') {
                $useridRegex = '/^PA[A-Z0-9]{6}$/';
                if (!preg_match($useridRegex, $userid)) {
                    $errors[] = "User ID for students/alumni must start with 'PA' followed by 6 alphanumeric characters (e.g., PA******).";
                }
            } elseif ($usertype === 'teacher' || $usertype === 'admin') {
                $useridRegex = ($usertype === 'admin') 
                    ? '/^PA@[A-Za-z0-9]{5,10}$/'
                    : '/^(PA@|PA)?[A-Za-z0-9]{5,10}$/';
                
                if (!preg_match($useridRegex, $userid)) {
                    $errors[] = ($usertype === 'admin') 
                        ? "User ID for admins must start with 'PA@' followed by 5-10 alphanumeric characters (e.g., PA@*****)."
                        : "User ID for teachers must be 5-10 alphanumeric characters and may optionally start with 'PA' or 'PA@' (e.g., PA@*****, PA*****, userid123).";
                }
            } else {
                $errors[] = "Invalid user type.";
            }
        }

        if (empty($username)) {
            $errors[] = "Username is required.";
        } elseif (!preg_match($usernameRegex, $username)) {
            $errors[] = "Username must be a name (letters only, optional space for full name, e.g., Alice or Alice Wonder).";
        }

        if (empty($password)) {
            $errors[] = "Password is required.";
        } elseif (!preg_match($passwordRegex, $password)) {
            $errors[] = "Password must be 8-12 characters (letters, numbers, or optional special characters @#*!_()).";
        }

        if (empty($usertype)) {
            $errors[] = "Please select a user type.";
        }

        if (empty($errors)) {
            try {
                $checkStmt = $conn->prepare("SELECT user_id FROM users_login WHERE user_id = ?");
                $checkStmt->bind_param("s", $userid);
                $checkStmt->execute();
                $result = $checkStmt->get_result();
                if ($result->num_rows > 0) {
                    $errors[] = "User ID already exists.";
                } else {
                    $stmt = $conn->prepare("INSERT INTO users_login (user_id, username, password, user_type) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssss", $userid, $username, $password, $usertype);
                    if ($stmt->execute()) {
                        echo "<script> alert ('Registration successful! Redirecting to login...')</script>";
                        echo "<script>
                                    signupForm.style.display = 'none';
                                    loginForm.style.display = 'block';
                                    forgotForm.style.display = 'none';
                              </script>";
                    } else {
                        echo "Error during registration: " . $stmt->error;
                    }
                    $stmt->close();
                }
                $checkStmt->close();
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            foreach ($errors as $error) {
                echo "<p>$error</p>";
            }
        }
    }

    // Handle Forgot Password (Change Password)
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['forgot'])) {
        $userid = trim($_POST['userid']);
        $old_password = trim($_POST['old_password']);
        $new_password = trim($_POST['new_password']);
        $errors = [];

        $useridRegex = '/^PA[A-Z0-9]{6}$/';
        $passwordRegex = '/^[a-zA-Z0-9@#*!_()]{8,12}$/';

        if (empty($userid)) {
            $errors[] = "User ID is required.";
        } elseif (!preg_match($useridRegex, $userid)) {
            $errors[] = "User ID must start with 'PA' followed by 6 alphanumeric characters (e.g., PA******).";
        }

        if (empty($old_password)) {
            $errors[] = "Old password is required. If forgotten ask for admin to assist!";
        }

        if (empty($new_password)) {
            $errors[] = "New password is required.";
        } elseif (!preg_match($passwordRegex, $new_password)) {
            $errors[] = "New password must be 8-12 characters (letters, numbers, or optional special characters @#*!_()).";
        }

        if (empty($errors)) {
            try {
                $checkStmt = $conn->prepare("SELECT user_id, password FROM users_login WHERE user_id = ?");
                $checkStmt->bind_param("s", $userid);
                $checkStmt->execute();
                $result = $checkStmt->get_result();

                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();
                    if ($old_password === $user['password']) {
                        $updateStmt = $conn->prepare("UPDATE users_login SET password = ? WHERE user_id = ?");
                        $updateStmt->bind_param("ss", $new_password, $userid);
    
                        if ($updateStmt->execute()) {
                            echo "Password updated successfully! Redirecting to login...";
                            echo "<script>
                                    setTimeout(() => {
                                        document.querySelector('.form.signup').style.display = 'none';
                                        document.querySelector('.form.login').style.display = 'block';
                                        document.querySelector('.form.forgot').style.display = 'none';
                                    }, 1500);
                                  </script>";
                        } else {
                            echo "Error updating password: " . $updateStmt->error;
                        }
                        $updateStmt->close();
                    } else {
                        echo "Incorrect old password.";
                    }
                } else {
                    echo "No account found with this User ID.";
                }
                $checkStmt->close();
            } catch (Exception $e) {
                echo "Error during password update: " . $e->getMessage();
            }
        } else {
            foreach ($errors as $error) {
                echo "<p>$error</p>";
            }
        }
    }

    $conn->close();
?>