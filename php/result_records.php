<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Set headers for JSON response and CORS
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, DELETE');
    header('Access-Control-Allow-Headers: Content-Type');

    include 'db_connection.php';

    if (!$conn) {
        echo json_encode(array("status" => "error", "message" => "Database connection failed: " . mysqli_connect_error()));
        exit;
    }

    // Handle DELETE request
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        $resultId = $data['student_id'] ?? null;
    
        if (!$resultId) {
            echo json_encode(array("status" => "error", "message" => "No student ID provided"));
            exit;
        }
    
        $sql = "DELETE FROM result_records WHERE student_id = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            echo json_encode(array("status" => "error", "message" => "Prepare failed: " . $conn->error));
            exit;
        }
        
        $stmt->bind_param("s", $resultId);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(array("status" => "success", "message" => "Result deleted successfully"));
            } else {
                echo json_encode(array("status" => "error", "message" => "No result found with that student ID"));
            }
        } else {
            echo json_encode(array("status" => "error", "message" => "Delete failed: " . $stmt->error));
        }
        
        $stmt->close();
        $conn->close();
        exit;
    }
    
    // Handle GET request
    $searchId = isset($_GET['student_id']) ? $_GET['student_id'] : null;
    
    if ($searchId) {
        $sql = "SELECT student_id, student_name, course, gpa, intake_year, status FROM result_records WHERE student_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(array("status" => "error", "message" => "Prepare failed: " . $conn->error));
            exit;
        }
        $stmt->bind_param("s", $searchId);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql = "SELECT student_id, student_name, course, gpa, intake_year, status FROM result_records";
        $result = $conn->query($sql);
        if (!$result) {
            echo json_encode(array("status" => "error", "message" => "Query failed: " . $conn->error));
            exit;
        }
    }
    
    $output = array();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $output[] = $row;
        }
        echo json_encode(array("status" => "success", "data" => $output));
    } else {
        echo json_encode(array("status" => "error", "message" => "No records found"));
    }
    
    $conn->close();
?>