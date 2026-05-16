<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Main Page</title>
</head>
<body>

<h2>
<?php
$hour = date("H");

if ($hour < 12) {
    echo "Good morning";
} elseif ($hour < 18) {
    echo "Good afternoon";
} else {
    echo "Good evening";
}

echo ", " . $_SESSION["username"] . "!";
?>
</h2>

<h3>Complete your application</h3>

<p>You need to pass a psychological test to continue.</p>

<a href="test.html">
  <button>Go to Test</button>
</a>

</body>
</html>