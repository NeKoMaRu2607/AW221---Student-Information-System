<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <title>School Information System - Student Result (Teacher)</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <style>
        div.clear-whitespace {margin-top: -.5rem;}
        div.border1 {border-top: 1px solid #000; margin-top: 1rem;}
        div.clear-whitespace1 {margin-top: -3rem;}
        .add {margin: 0 20px;}
        .add table {border: 1px solid black;}
        .add tr {margin: -100px 0;}
        .add label {font-size: 15px; margin-left: 10px; margin-right: -10px;}
        .add input {font-size: 12px; padding: 5px;margin-right: 8px;}
        .add button {font-size: 15px; margin: 10px; background-color: green; padding: 5px 10px; color: #fff; border-radius: 10px;}
        @media (max-width:1080px) {span {font-size: 70%;} #text1 {font-size: 70%;}}
        @media (max-width:768px) {div.rounded {margin: 10px 10px;} div.border1 {margin-top: -.2rem; margin-left: 10px; margin-right: 10px;} span {font-size: 56%;} #text1 {font-size: 56%;}}
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter var', ...defaultTheme.fontFamily.sans],
                    },
                }
            }
        }
    </script>
</head>

<body class="antialiased">

<?php
    session_start();
    include 'db_connection.php';

    // Credits
    // Per Semester
    // Minimum Credit Hours: 12
    // Maximum Credit Hours: 18

    // Weightage
    // Identify by the weight of each assessment
    // Minimum Passing Marks: 40%
    // Maximum Passing Marks: 100%

    // PHP functions for grade calculation
    function calculateGP($score) {
        if ($score > 100 || $score < 0) return 0.0;
        if ($score >= 90) return 4.0;
        if ($score >= 85) return 3.67;
        if ($score >= 80) return 3.33;
        if ($score >= 75) return 3.00;
        if ($score >= 70) return 2.67;
        if ($score >= 65) return 2.33;
        if ($score >= 60) return 2.00;
        if ($score >= 55) return 1.67;
        if ($score >= 50) return 1.33;
        if ($score >= 40) return 1.00;
        if ($score >= 1) return 0.67;
        return 0.0;
    }

    function calculateGrade($score) {
        if ($score > 100 || $score < 0) return "F";
        if ($score >= 90) return "A+";
        if ($score >= 85) return "A";
        if ($score >= 80) return "A-";
        if ($score >= 75) return "B+";
        if ($score >= 70) return "B";
        if ($score >= 65) return "B-";
        if ($score >= 60) return "C+";
        if ($score >= 55) return "C";
        if ($score >= 50) return "C-";
        if ($score >= 40) return "D";
        if ($score >= 1) return "F";
        return "F";
    }

    // Initialize variables
    $studentData = null;
    $results = []; // This will now be an in-memory array instead of database-driven
    if (isset($_POST['student_id'])) {
        $studentId = $_POST['student_id'];
        $stmt = $conn->prepare("SELECT * FROM result_records WHERE student_id = ?");
        $stmt->bind_param("s", $studentId);
        $stmt->execute();
        $studentData = $stmt->get_result()->fetch_assoc();

        // Load results from session if they exist (for this session only)
        if (isset($_SESSION['results'][$studentId])) {
            $results = $_SESSION['results'][$studentId];
        }
    }

    // Handle new result submission
    if (isset($_POST['submit_result'])) {
        $studentId = $_POST['student_id'];
        $code = $_POST['code'];
        $course = $_POST['course'];
        $weightage = (float)$_POST['weightage'];
        $score = (float)$_POST['score'];
        $credits = (int)$_POST['credits'];
        $gp = calculateGP($score);
        $grade = calculateGrade($score);

        // Check for duplicates in the display table only
        $isDuplicate = false;
        if (isset($_SESSION['results'][$studentId])) {
            foreach ($_SESSION['results'][$studentId] as $existingResult) {
                if ($existingResult['code'] === $code || $existingResult['course'] === $course) {
                    $isDuplicate = true;
                    // Display error message but don't exit the process
                    echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative' role='alert'>
                            <strong class='font-bold'>Warning!</strong>
                            <span class='block sm:inline'> A result with course code '$code' or course name '$course' already exists in the report!</span>
                          </div>";
                    break;
                }
            }
        }

        // Add new result to in-memory array (not database)
        if (!isset($_SESSION['results'][$studentId])) {
            $_SESSION['results'][$studentId] = [];
        }

        // Only add to the display table if not a duplicate
        if (!$isDuplicate) {
            $_SESSION['results'][$studentId][] = [
                'code' => $code,
                'course' => $course,
                'weightage' => $weightage,
                'score' => $score,
                'credits' => $credits,
                'gp' => $gp,
                'grade' => $grade
            ];
        }

        // Calculate GPA and update database
        $results = $_SESSION['results'][$studentId];
        $totalWeightage = 0;
        $totalGP = 0;
        foreach ($results as $result) {
            $totalWeightage += $result['weightage'];
            $totalGP += $result['gp'] * $result['credits'];
        }
        $gpa = $totalWeightage > 0 ? $totalGP / $totalWeightage : 0;

        // Update or insert student data with new GPA            !!!!!!!!!!!!          (UPDATEEEEEE)        !!!!!!!!!!!!!
        if ($studentData) {
            $courseToUpdate = $_POST['course'] ?? ($studentData['course'] ?? 'Unknown');
            $stmt = $conn->prepare("UPDATE result_records SET course = ?, gpa = ? WHERE student_id = ?");
            $stmt->bind_param("sds", $courseToUpdate, $gpa, $studentId);
        } else {
            $stmt = $conn->prepare("INSERT INTO result_records (student_id, course, gpa) VALUES (?, ?, ?)");
            $stmt->bind_param("ssd", $studentId, $courseToUpdate, $gpa);
        }
        $stmt->execute();

        // Refresh student data
        $stmt = $conn->prepare("SELECT * FROM result_records WHERE student_id = ?");
        $stmt->bind_param("s", $studentId);
        $stmt->execute();
        $studentData = $stmt->get_result()->fetch_assoc();
    }

    // Calculate aggregates for display
    $totalWeightage = 0;
    $cumulativeWeightage = 0;
    $totalGP = 0;
    $cumulativeGP = 0;
    foreach ($results as $result) {
        $totalWeightage += $result['weightage'];
        $totalGP += $result['gp'] * $result['credits'];
    }
    $gpa = $totalWeightage > 0 ? $totalGP / $totalWeightage : 0;
    $cgpa = $gpa; // Simplified for one semester
?>

    <div class="prose lg:prose-2xl mx-auto my-2 border rounded border-blue-500">
        <div class="flex flex-nowrap justify-center">
            <div class="grid grid-rows-auto gap-0">

                <!-- School Header Starts-->
                <div id="schoolHeader" class="flex justify-around items-center px-0">

                    <div class="grid grid-rows-auto gap-1 py-2 text-center rounded ">
                        <span class="text-3xl font-semibold uppercase">Pinnacle Academy</span>
                        <span class="text-base">
                            No. 6, Persiaran Elektron, 47000, Selangor<br>
                            Tel: +60 12-345 6789 <br>
                        </span>
                        <span class="text-2xl font-semibold uppercase">Report Sheet</span>
                    </div>
                </div>
                <!-- School Header Ends-->

                <div class="border1"></div>

                <!-- Student Profile Starts-->
                <div id="studentProfile" class="w-full p-3 clear-whitespace">
                    <form method="POST">
                        <label for="student_id">Student ID:</label>
                        <input type="text" name="student_id" id="student_id" value="<?php echo isset($_POST['student_id']) ? $_POST['student_id'] : ''; ?>">
                        <button type="submit">Search</button>
                    </form>

                    <?php if ($studentData): ?>
                    <table
                        class="table-auto w-full border-collapse border border-slate-400 text-center text-xs md:text-sm">
                        <tbody>
                            <tr class="bg-blue-100">
                                <th class="border border-slate-400 px-2 py-1" style="text-align: center;">STUDENT ID
                                </th>
                                <th class="border border-slate-400 px-2 py-1" style="text-align: center;">NAME OF
                                    STUDENT</th>
                                <th class="border border-slate-400 px-2 py-1" style="text-align: center;">EMAIL
                                </th>
                            </tr>
                            <tr class="even:bg-gray-100">
                                <td class="border border-slate-400 px-2 py-1" style="text-align: center;"><?php echo $studentData['student_id']; ?></td>
                                <td class="border border-slate-400 px-2 py-1" style="text-align: center;"><?php echo $studentData['student_name']; ?></td>
                                <td class="border border-slate-400 px-2 py-1" style="text-align: center;"><?php echo $studentData['email']; ?></td>
                            </tr>
                            <tr class="bg-blue-100">
                                <th class="border border-slate-400 px-2 py-1" style="text-align: center;">PHONE NUMBER
                                </th>
                                <th class="border border-slate-400 px-2 py-1" colspan="2" style="text-align: center;">
                                    PROGRAMME</th>
                            </tr>
                            <tr class="even:bg-gray-100">
                                <td class="border border-slate-400 px-2 py-1" style="text-align: center;"><?php echo $studentData['phone']; ?></td>
                                <td class="border border-slate-400 px-2 py-1" colspan="2" style="text-align: center;"><?php echo $studentData['course']; ?></td>
                            </tr>
                            <tr class="bg-blue-100">
                                <th class="border border-slate-400 px-2 py-1" style="text-align: center;">INTAKE</th>
                                <th class="border border-slate-400 px-2 py-1" style="text-align: center;">BRANCH / CAMPUS</th>
                                <th class="border border-slate-400 px-2 py-1" style="text-align: center;">PRINT DATE
                                </th>
                            </tr>
                            <tr class="even:bg-gray-100">
                                <td class="border border-slate-400 px-2 py-1" style="text-align: center;"><?php echo $studentData['intake_year']; ?></td>
                                <td class="border border-slate-400 px-2 py-1" style="text-align: center;">Campus Selangor</td>
                                <td class="border border-slate-400 px-2 py-1" style="text-align: center;"><?php echo date('d M, Y'); ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
                <!-- Student Profile Ends -->

                <!-- Academic Record Starts -->
                <?php if ($studentData): ?>
                <div id="academicRecord" class="w-full p-3 clear-whitespace1">
                    <table
                        class="table-auto w-full border-collapse border border-slate-400 text-center text-xs md:text-sm"
                        id="tblData">
                        <thead>
                            <tr class="bg-blue-100 add-row">
                                <th class="border border-slate-400 px-2 py-1" style="text-align: center;">CODE</th>
                                <th class="border border-slate-400 px-2 py-1" style="text-align: center;">COURSE DESCRIPTION</th>
                                <th class="border border-slate-400 px-2 py-1" style="text-align: center;">WEIGHTAGE</th>
                                <th class="border border-slate-400 px-2 py-1" style="text-align: center;">SCORE</th>
                                <th class="border border-slate-400 px-2 py-1" style="text-align: center;">CREDITS</th>
                                <th class="border border-slate-400 px-2 py-1" style="text-align: center;">GP </th>
                                <th class="border border-slate-400 px-2 py-1" style="text-align: center;">TOTAL RESULT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $result): ?>
                                <tr class="even:bg-gray-100">
                                    <td class="border border-slate-400 px-2 py-1" style="text-align: center;"><?php echo $result['code']; ?></td>
                                    <td class="border border-slate-400 px-2 py-1" style="text-align: center;"><?php echo $result['course']; ?></td>
                                    <td class="border border-slate-400 px-2 py-1" style="text-align: center;"><?php echo $result['weightage']; ?></td>
                                    <td class="border border-slate-400 px-2 py-1" style="text-align: center;"><?php echo $result['score']; ?></td>
                                    <td class="border border-slate-400 px-2 py-1" style="text-align: center;"><?php echo $result['credits']; ?></td>
                                    <td class="border border-slate-400 px-2 py-1" style="text-align: center;"><?php echo number_format($result['gp'], 2); ?></td>
                                    <td class="border border-slate-400 px-2 py-1" style="text-align: center;"><?php echo $result['grade']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Class Aggregates Starts -->
                    <div class="w-full">
                        <table
                            class="table-auto w-full border-collapse border border-slate-400 text-center text-xs md:text-sm">
                            <thead>
                                <tr class="bg-blue-100">
                                    <th class="border border-slate-400"
                                        style="padding: 5px 10px 5px 5px; justify-content: center; text-align: center;">
                                        Total Weightage for Current Semester</th>
                                    <th class="border border-slate-400"
                                        style="justify-content: center; text-align: center;">Cumulative Weightage</th>
                                    <th class="border border-slate-400"
                                        style="padding: 5px 10px 5px 5px; justify-content: center; text-align: center;">
                                        Total Grade Point for Current Semester</th>
                                    <th class="border border-slate-400"
                                        style="justify-content: center; text-align: center;">Cumulative Grade Point</th>
                                    <th class="border border-slate-400"
                                        style="justify-content: center; text-align: center;">Grade Point Average (GPA)
                                    </th>
                                    <th class="border border-slate-400"
                                        style="padding: 5px; justify-content: center; text-align: center;">Cumulative
                                        Grade Point Average (CGPA)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="even:bg-gray-100">
                                    <td class="border border-slate-400" style="text-align: center;"><?php echo number_format($totalWeightage, 2); ?></td>
                                    <td class="border border-slate-400" style="text-align: center;"><?php echo number_format($cumulativeWeightage, 2); ?></td>
                                    <td class="border border-slate-400" style="text-align: center;"><?php echo number_format($totalGP, 2); ?></td>
                                    <td class="border border-slate-400" style="text-align: center;"><?php echo number_format($cumulativeGP, 2); ?></td>
                                    <td class="border border-slate-400" style="text-align: center;"><?php echo number_format($gpa, 2); ?></td>
                                    <td class="border border-slate-400" style="text-align: center;"><?php echo number_format($cgpa, 2); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Class Aggregates & Academic Record Ends -->

                <!-- Add Result Form -->
                <div class="add">
                    <form method="POST">
                        <input type="hidden" name="student_id" value="<?php echo $studentData['student_id']; ?>">
                        <table>
                            <h4>Add Result</h4>
                            <tr>
                                <td><label for="code">Code:</label></td>
                                <td><input type="text" name="code" id="code"></td>
                                <td><label for="course">Course:</label></td>
                                <td><input type="text" name="course" id="course"></td>
                                <td><label for="weightage">Weightage:</label></td>
                                <td><input type="text" name="weightage" id="weightage"></td>
                            </tr>
                            <tr>
                                <td><label for="score">Score:</label></td>
                                <td><input type="text" name="score" id="score"></td>
                                <td><label for="credits">Credits:</label></td>
                                <td><input type="text" name="credits" id="credits"></td>
                                <td><button type="submit" name="submit_result">Submit</button></td>
                            </tr>
                        </table>
                    </form>
                </div>
                <?php endif; ?>
    </div>
    <?php $conn->close(); ?>
</body>
</html>