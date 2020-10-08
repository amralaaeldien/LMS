<?php 
	session_start();
	require('pdo.php');
	$stmt = $conn->prepare("SELECT * from courses");
	$executed = $stmt->execute();
	if($executed){
		$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
 ?>

 <!DOCTYPE html>
 <html>
 <head>
 	<title>index</title>
 </head>
 <body>
 	<div>
 		Menu : <a href="list_users.php">List of users</a>
 	</div>
 	<?php  echo '<br>', 'hello '.$_SESSION['username'].' !'; ?>
 	<h1>courses</h1>
 <?php
 	foreach ($courses as $course) {
 		foreach ($course as $key => $value) {
 			if ($key !== 'id') {
 				echo '<br>';
	 			if ($key === 'title'){
		 			echo $key,' : ', '<a href="/course.php?id='.$id.'">'.$value.'</a>';
				} else {
					echo $key, ' : ' ,$value;
				};
	 			echo '<br>'; 		
	 		} else {
	 			$id = $value;
	 		};
 	};
 }
  ?>
 </body>
 </html>