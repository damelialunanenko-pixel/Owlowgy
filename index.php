<?php
session_start();
include "connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_POST["user_id"];
    $user_type = $_POST["user_type"];

    $personality = $_POST["q2"];
    
    $learning = $_POST["q1"];

    $pace = $_POST["q3"];

    $test_id = "PT" . rand(100,999);

  
    $sql = "INSERT INTO PsychologicalTestResults 
    (Test_ID, User_Type, User_ID, Personality_Type, Learning_Style, Preferred_Pace)
    VALUES 
    ('$test_id', '$user_type', '$user_id', '$personality', '$learning', '$pace')";

    if ($conn->query($sql) === TRUE) {
        echo " Test saved successfully";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

