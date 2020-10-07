<?php 
	require('pdo.php');
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

	if($_SERVER['REQUEST_METHOD'] === 'POST'){
		if (isset($_POST['Do'])) {
			$step_id = $_POST['Do'];
			$stmt = $conn->prepare("UPDATE steps SET is_done = true where id = :step_id ;");
			$stmt->bindParam(":step_id", $step_id);
			$stmt->execute();
			header("Refresh:0");
		} else if (isset($_POST['Undo'])) {
			$step_id = $_POST['Undo'];
			$stmt = $conn->prepare("UPDATE steps SET is_done = false where id = :step_id ;");
			$stmt->bindParam(":step_id", $step_id);
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
	<?php echo '<br><a href="/index2.php">back</a><br> <br>' ?>
	<?php if(isset($steps)) { ?>
		<?php foreach ($steps as $step) {
			foreach ($step as $key => $value) {
				if($key ==='id'){
					$id = $value;
				}else if ($key !== 'id' and $key !== 'course' and $key !== 'is_done') {

					echo $key, ' : ', $value;
					echo '<br>';
				} else if ( $key === 'is_done'){
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