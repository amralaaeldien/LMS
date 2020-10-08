<?php
	require('pdo.php');
	if($_POST['message']){
		$stmt = $conn->prepare("INSERT INTO messages (content, sender, receiver) values (:message, 	:current_userid, :other_userid)");
		$stmt->bindParam(":message", $_POST['message']);
		$stmt->bindParam(":current_userid", $_POST['current_userid']);
		$stmt->bindParam(":other_userid", $_POST['other_userid']);
		$stmt->execute();



		$stmt = $conn->prepare("select sent_at from messages where id = LAST_INSERT_ID();");
		if($stmt->execute()){
			$timestamp = $stmt->fetch(PDO::FETCH_ASSOC)['sent_at'];
		}

		$stmt = $conn->prepare("INSERT INTO notifications (user, description) values (:other_userid, :description);");
		$stmt->bindParam(":other_userid", $_POST['other_userid']);
		$description =  $_POST['current_username'] . " sent you a message at " . $timestamp;
		$stmt->bindParam(":description",$description);
		$stmt->execute();

		echo '<br>', 'You is saying : ', $_POST['message'];
		echo '<br>', 'sent at ', $timestamp, '<br>';
	}



?>