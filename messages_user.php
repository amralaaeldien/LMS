<?php 
	session_start();
	require('pdo.php');
	$current_username = $_SESSION['username'];
	$other_username = $_GET['user'];

	$stmt = $conn->prepare("SELECT id from users where username = :username ;");
	$stmt->bindParam(":username", $current_username);
	if ($stmt->execute()){
		$current_userid = $stmt->fetch(PDO::FETCH_ASSOC);
		$current_userid = $current_userid['id'];
	}

	$stmt = $conn->prepare("SELECT id from users where username = :username ;");
	$stmt->bindParam(":username", $other_username);
	if ($stmt->execute()){
		$other_userid = $stmt->fetch(PDO::FETCH_ASSOC);
		$other_userid = $other_userid['id'];
	}

	if($_SERVER['REQUEST_METHOD'] === 'GET'){
		$stmt = $conn->prepare("SELECT content, sender, receiver, sent_at from messages where (sender = :current_userid and receiver = :other_userid) or (sender = :other_userid and receiver = :current_userid ) order by sent_at asc");
		$stmt->bindParam(":current_userid", $current_userid);
		$stmt->bindParam(":other_userid", $other_userid);
		if ($stmt->execute()){
			$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}

	}
 ?>
<!DOCTYPE html>
<html>
<head>
	<title>messages between you and 
		<?php echo $other_user; ?>
	</title>
</head>
<body>
<div id='messages'>
	<?php 
	foreach ($messages as $message) {
		foreach ($message as $key => $value) {
			if($key === 'content'){
				$content = $value;
			} else if ($key === 'sender') {
				if($value === $current_userid){
					echo '<br>', 'You is saying : ', $content;
				} else {
					echo '<br>', $other_username, ' is saying : ', $content;
				}

			} else if ($key === 'sent_at') {
				echo '<br>', 'sent at ', $value, '<br>';
			}
		}
	}
	?>
</div>
<br>
<form method="post" id="msg-form">
	<input type="text" name="message">
	<button type="submit" form="msg-form" name="Send">Send</button>
</form>
</body>
</html>