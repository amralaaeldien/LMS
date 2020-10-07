<?php

require('pdo.php');
if($_SERVER['REQUEST_METHOD'] === 'POST'){
	$errors = [];

	if(!isset($_POST['username']) or (strlen($_POST['username']) === 0) ){
		array_push($errors, 'username should be provided');
	}else if (strlen($_POST['username']) >50){
		array_push($errors, 'username should be less than 50');
	};

	if(!isset($_POST['password']) or (strlen($_POST['password']) === 0)){
		array_push($errors, 'password should be provided');
	}else if (strlen($_POST['password']) >50){
		array_push($errors, 'password should be less than 50');
	};
	if( ( isset($_POST['username']) and (strlen($_POST['username']) > 0) ) and (isset($_POST['password']) and (strlen($_POST['password']) > 0) ) ) {
		$stmt = $conn->prepare("SELECT username from users where username = :username and password = :password ");
		$stmt->bindParam(":username", $_POST['username']);
		$stmt->bindParam(":password", $_POST['password']);
		$executed = $stmt->execute();
		if($executed){
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
		}
		if(!$result){
			array_push($errors, 'Username or password is wrong');
		} else {
			session_start();
			$_SESSION["username"] = $_POST['username'];
			header('Location: index2.php');
			return;
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>login</title>
</head>
<body>
<h1>Login</h1>

<?php if(isset($errors)){ ?>
	<?php foreach ($errors as $value) {
		print_r( "<p>".$value ."</p>"); }; ?>
<?php } ?>

<form method="post">
	<label>username</label> <input type="text" name="username"> <br>
	<label>password</label> <input type="text" name="password"><br>
	<input type="submit"><br>

	<a href="register.php">Register Here</a>
</form>
</body>
</html>