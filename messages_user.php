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
		<?php echo $other_username; ?>
	</title>
	<script
  		src="https://code.jquery.com/jquery-3.5.1.min.js"
  		integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  		crossorigin="anonymous">
  </script>
  <script>
  	$(document).ready(function() {
  		$("#msg-form").submit(function(e){
			    return false;
			});
  		$("button").click(function(){
  			var message = $("input").val();
			document.getElementById("msg-form").reset();
  			$.post("send_message.php", {
  				message : message,
  				current_username : '<?php echo $current_username; ?>',
  				other_username: '<?php echo $other_username ;?>',
  				current_userid : '<?php echo $current_userid; ?>',
  				other_userid: '<?php echo $other_userid ;?>'
  			}, function(data, status){
  				$('#messages').append(data);
  			});
  		});
  	})
  </script>
</head>
<body>
		<br><a href="/index2.php">index</a><br> <br>

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
<form id="msg-form">
	<input type="text" name="message" id='input'>
	<button type="submit" form="msg-form" name="Send">Send</button>
</form>
</body>
</html>