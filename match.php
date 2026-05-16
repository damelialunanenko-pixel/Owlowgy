<?php
session_start();

$conn = new mysqli("localhost", "root", "", "owlogy");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['student_id'])) {
    die("Student not logged in");
}

$student_id = $_SESSION['student_id'];


if (isset($_GET['select_teacher'])) {

    $selected_teacher = $_GET['select_teacher'];

    $conn->query("
        UPDATE enrollment
        SET teacher_id='$selected_teacher'
        WHERE student_id='$student_id'
    ");

    header("Location: mainpage.php");
    exit();
}

$student_test = $conn->query("
SELECT * FROM psychologicaltestresults
WHERE user_id='$student_id' AND user_type='Student'
")->fetch_assoc();

$enroll = $conn->query("
SELECT * FROM enrollment
WHERE student_id='$student_id'
")->fetch_assoc();

$student_course = $enroll['course_id'] ?? null;

$teachers_result = $conn->query("
SELECT teacher_id, teacher_name, qualification
FROM teachers
");

$teachers = [];


while ($t = $teachers_result->fetch_assoc()) {

    $score = 0;

    
    $teacher_test = $conn->query("
        SELECT * FROM psychologicaltestresults
        WHERE user_id='{$t['teacher_id']}' AND user_type='Teacher'
    ")->fetch_assoc();

    if ($teacher_test && $student_test) {

       
        $criteria = ['personality_type', 'learning_style', 'preferred_pace'];

        foreach ($criteria as $c) {
            if (($student_test[$c] ?? '') == ($teacher_test[$c] ?? '')) {
                if ($c == 'preferred_pace') {
                    $score += 1;
                } else {
                    $score += 2;
                }
            }
        }
    }

    
    $course_match = $conn->query("
        SELECT * FROM courseoffering
        WHERE teacher_id='{$t['teacher_id']}'
        AND course_id='$student_course'
    ");

    if ($course_match && $course_match->num_rows > 0) {
        $score += 3;
    }

    $teachers[] = [
        "id" => $t['teacher_id'],
        "name" => $t['teacher_name'],
        "qualification" => $t['qualification'],
        "score" => $score
    ];
}

$n = count($teachers);

for ($i = 0; $i < $n - 1; $i++) {
    for ($j = 0; $j < $n - $i - 1; $j++) {

        if ($teachers[$j]['score'] < $teachers[$j + 1]['score']) {

            $temp = $teachers[$j];
            $teachers[$j] = $teachers[$j + 1];
            $teachers[$j + 1] = $temp;
        }
    }
}

$max_score = 10;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Match Teachers</title>
</head>
<body>

<h2>Best Teacher Matches</h2>

<a href="match.php"><button>More variants</button></a>

<hr>

<?php
for ($i = 0; $i < 5 && $i < count($teachers); $i++) {

    $t = $teachers[$i];
    $percent = round(($t['score'] / $max_score) * 100);

    echo "<div style='border:1px solid #ccc; padding:10px; margin:10px;'>";

    echo "<h3>{$t['name']}</h3>";
    echo "<p>Qualification: {$t['qualification']}</p>";
    echo "<p>Score: {$t['score']}</p>";
    echo "<p>Match: {$percent}%</p>";

    echo "<a href='?select_teacher={$t['id']}'>
            <button>Select teacher</button>
          </a>";

    echo "</div>";
}
?>

</body>
</html>