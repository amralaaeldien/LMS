<?php 
	require('pdo.php');
	session_start();
	$stmt = $conn->prepare("SELECT title from courses where id = :course_id ;");
	$stmt->bindParam(":course_id", $_GET['id']);
	if ($stmt->execute()){
		$course_title = $stmt->fetch(PDO::FETCH_ASSOC);
	}
	$stmt = $conn->prepare("SELECT * from steps where course = :course_id;");
	$stmt->bindParam(":course_id", $_GET['id']);
	$executed = $stmt->execute();
	if($executed){
		$steps = $stmt->fetchAll(PDO::FETCH_ASSOC);

	}
	$stmt = $conn->prepare("SELECT id from users where username = :username ;");
	$stmt->bindParam(":username", $_SESSION['username']);
	if ($stmt->execute()){
		$user_id = $stmt->fetch(PDO::FETCH_ASSOC);
		$user_id = $user_id['id'];
	}
	$is_done_arr =[];
	foreach ($steps as $step) {
		foreach ($step as $key => $value) {
			if($key === 'id'){
				$stmt = $conn->prepare("SELECT is_done from steps_and_users where user_id = :user_id and step_id = :step_id ;");
				$stmt->bindParam(":user_id", $user_id);
				$stmt->bindParam(":step_id", $value);
				$executed = $stmt->execute();
				if($executed){
					$is_done = $stmt->fetch(PDO::FETCH_ASSOC);
					if($is_done)
						$is_done = $is_done['is_done'];
					if(!$is_done){
						$is_done = 0;
						$stmt = $conn->prepare("INSERT into steps_and_users (step_id, user_id, is_done)  VALUES (:step_id, :user_id, :is_done)");
						$stmt->bindParam(":step_id", $value);
						$stmt->bindParam(":user_id", $user_id);
						$stmt->bindParam(":is_done", $is_done);
						try {
							$stmt->execute();
						} catch (Exception $e) {

						}
					}
					$is_done_arr[$value-1] = $is_done;
				}
			}
		}
	}

	if($_SERVER['REQUEST_METHOD'] === 'POST'){
		if (isset($_POST['Do'])) {
			$step_id = $_POST['Do'];
			$stmt = $conn->prepare("UPDATE steps_and_users SET is_done = true where step_id = :step_id and user_id = :user_id ;");
			$stmt->bindParam(":step_id", $step_id);
			$stmt->bindParam(":user_id", $user_id);
			$stmt->execute();
			header("Refresh:0");
		} else if (isset($_POST['Undo'])) {
			$step_id = $_POST['Undo'];
			$stmt = $conn->prepare("UPDATE steps_and_users SET is_done = false where step_id = :step_id and user_id = :user_id ;");
			$stmt->bindParam(":step_id", $step_id);
			$stmt->bindParam(":user_id", $user_id);
			$stmt->execute();
			header("Refresh:0");
		}
	}

?>
<!DOCTYPE html>
<html>
<head>
	<title>steps of course
		<?php echo $course_title['title']; ?>
	 </title>
</head>
<body>
	<br><a href="/index2.php">back</a><br> <br>
	<?php if(isset($steps)) { ?>
		<?php foreach ($steps as $step) {
			foreach ($step as $key => $value) {
				if($key ==='id'){
					$id = $value;
				}else if ($key !== 'course' and $key !== 'is_done') {
					echo $key, ' : ', $value;
					echo '<br>';
				}
			}	
			foreach ($is_done_arr as $key => $value) {
				# code...
				if($key ===($id-1) ){
					if(!$value){
						echo '
						<form method="post" id="f-'.$id.'">
						<button form="f-'.$id.'" type="submit" name="Do" value="'.$id.'">Done</button>
						</form>
						';
						echo '<br>';
					} else {
						echo '
						<form method="post" id = "f-'.$id.'">
						<button form="f-'.$id.'" type="submit" name="Undo" value="'.$id.'">Undo</button>
						</form>
						';
						echo '<br>';					
					}						
				}
			}
				
			
			
		} 
	 }?>
</body>
</html>