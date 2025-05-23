<?php
session_start();
include_once '../../includes/db_connection.php';
include 'dashboardfunc.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>

<body>
    <header>
        <?php include 'header.php'; ?>
    </header>
    <div class="container">
        <?php include 'sidebar.php'; ?>
        <div class="content">
            <h2>GRADES</h2>
            <h3 style="width: fit-content">School Year: <?php echo htmlspecialchars($sy_display); ?></h3>
            <table>
                <tr>
                    <th>Subject</th>
                    <th>1st Grading</th>
                    <th>2nd Grading</th>
                    <th>3rd Grading</th>
                    <th>4th Grading</th>
                    <th>Average</th>
                    <th>Remarks</th>
                </tr>
                <?php
                // Fetch the student's grades
                $sql = "SELECT s.subject_name, g.`1stGrading`, g.`2ndGrading`, g.`3rdGrading`, g.`4thGrading`
                        FROM grades g
                        JOIN subject s ON g.subject_id = s.id
                        WHERE g.student_id = ? AND g.school_year_id = ?
                        ORDER BY s.subject_name";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $_SESSION['user_id'], $school_year_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0): ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">Student Not Enrolled</td>
                    </tr>
                <?php
                else:
                    while ($row = $result->fetch_assoc()):
                        $average = ($row['1stGrading'] + $row['2ndGrading'] + $row['3rdGrading'] + $row['4thGrading']) / 4;
                        $average = round($average);

                        // Determine remarks and CSS class
                        if ($row['1stGrading'] == 0 || $row['2ndGrading'] == 0 || $row['3rdGrading'] == 0 || $row['4thGrading'] == 0) {
                            $remarks = "IN PROCESS";
                            $class = "in-process";
                        } else {
                            if ($average >= 75) {
                                $remarks = "PASSED";
                                $class = "passed";
                            } else {
                                $remarks = "FAILED";
                                $class = "failed";
                            }
                        }
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['1stGrading']); ?></td>
                        <td><?php echo htmlspecialchars($row['2ndGrading']); ?></td>
                        <td><?php echo htmlspecialchars($row['3rdGrading']); ?></td>
                        <td><?php echo htmlspecialchars($row['4thGrading']); ?></td>
                        <td><?php echo htmlspecialchars($average); ?></td>
                        <td>
                            <span class="remarks <?php echo $class; ?>">
                                <?php echo $remarks; ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile;
                endif; ?>
            </table>
        </div>
    </div>
    <script src="../../includes/timeout.js"></script>
</body>

</html>