<?php 
	require('pdo.php');
	session_start();
	$stmt = $conn->prepare("SELECT username from users where username != :current_username;");
	$stmt->bindParam(":current_username", $_SESSION['username']);
	if ($stmt->execute()){
		$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>list of users</title>
</head>
<body>
<ul>
<br><a href="/index2.php">back</a><br> <br>
<h1>List of users</h1>
	<?php 
	foreach ($users as $user) {
		foreach ($user as $key => $value) {
			echo '<li>', $value, '</li>';
		}
	} ?>

</ul>
</body>
</html>