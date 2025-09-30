<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    // Set headers for JSON response and CORS
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, DELETE');
    header('Access-Control-Allow-Headers: Content-Type');
    
    // student_profile.php
    include 'db_connection.php';

    // Helper function to calculate age
    function calculateAge($birth_date) {
        $dob = new DateTime($birth_date);
        $today = new DateTime();
        $age = $today->diff($dob);
        return $age->y;
    }

    if (!$conn) {
        echo json_encode(array("status" => "error", "message" => "Database connection failed: " . mysqli_connect_error()));
        exit;
    }

    // Handle DELETE request
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        $studentId = $data['id'] ?? null;
    
        if (!$studentId) {
            echo json_encode(array("status" => "error", "message" => "No student ID provided"));
            exit;
        }
    
        $sql = "DELETE FROM student_profiles WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            echo json_encode(array("status" => "error", "message" => "Prepare failed: " . $conn->error));
            exit;
        }
        
        $stmt->bind_param("s", $studentId);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(array("status" => "success", "message" => "Student deleted successfully"));
            } else {
                echo json_encode(array("status" => "error", "message" => "No student found with that ID"));
            }
        } else {
            echo json_encode(array("status" => "error", "message" => "Delete failed: " . $stmt->error));
        }
        
        $stmt->close();
        $conn->close();
        exit;
    }

    $searchId = isset($_GET['id']) ? $_GET['id'] : null;

    if ($searchId) {
        $sql = "SELECT id, student_name, email, phone, birth_date FROM student_profiles WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(array("status" => "error", "message" => "Prepare failed: " . $conn->error));
            exit;
        }
        $stmt->bind_param("s", $searchId);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql = "SELECT id, student_name, email, phone, birth_date FROM student_profiles";
        $result = $conn->query($sql);
        if (!$result) {
            echo json_encode(array("status" => "error", "message" => "Query failed: " . $conn->error));
            exit;
        }
    }

    $output = array();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['age'] = calculateAge($row['birth_date']);
            $output[] = $row;
        }
        echo json_encode(array("status" => "success", "data" => $output));
    } else {
        echo json_encode(array("status" => "error", "message" => "No records found"));
    }

    $conn->close();
?>