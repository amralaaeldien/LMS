<?php 
	session_start();
	require('pdo.php');
	$stmt = $conn->prepare("SELECT * from courses");
	$executed = $stmt->execute();
	if($executed){
		$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	$stmt = $conn->prepare("SELECT id from users where username = :current_username");
	$stmt->bindParam(':current_username', $_SESSION['username']);
	if($stmt->execute()){
		$current_userid = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
	}
	$stmt = $conn->prepare("SELECT count(*) from Notifications where user = :current_userid and is_clicked = false; ");
	$stmt->bindParam(":current_userid", $current_userid);
	if($stmt->execute()){
		$count = $stmt->fetch(PDO::FETCH_ASSOC)['count(*)'];
	}

	$stmt = $conn->prepare("SELECT * from notifications where user = :current_userid;");
	$stmt->bindParam(":current_userid", $current_userid);
	if($stmt->execute()){
		$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	$stmt = $conn->prepare("SELECT sender from messages where receiver = :current_userid;");
	$stmt->bindParam(":current_userid", $current_userid);
	if($stmt->execute()){
		$other_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

 ?>

 <!DOCTYPE html>
 <html>
 <head>
 	<title>index</title>
 	<style>
 		
		 		/* Dropdown Button */
		.dropbtn {
		  background-color: #4CAF50;
		  color: white;
		  padding: 16px;
		  font-size: 16px;
		  border: none;
		}

		/* The container <div> - needed to position the dropdown content */
		.dropdown {
		  position: relative;
		  display: inline-block;
		}

		/* Dropdown Content (Hidden by Default) */
		.dropdown-content {
		  display: none;
		  position: absolute;
		  background-color: #f1f1f1;
		  min-width: 160px;
		  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
		  z-index: 1;
		}

		/* Links inside the dropdown */
		.dropdown-content a {
		  color: black;
		  padding: 12px 16px;
		  text-decoration: none;
		  display: block;
		}

		/* Change color of dropdown links on hover */
		.dropdown-content a:hover {background-color: #ddd;}

		/* Show the dropdown menu on hover */
		.dropdown:hover .dropdown-content {display: block;}

		/* Change the background color of the dropdown button when the dropdown content is shown */
		.dropdown:hover .dropbtn {background-color: #3e8e41;}
 	</style>
 </head>
 <body>
 	<div>
 		Menu : <a href="list_users.php">List of users</a>
 		<span class="dropdown">
	 		<button class="dropbtn">Notifications(

	 			<?php echo $count; ?>
	 		)</button>
				<div class="dropdown-content">
					<?php 
						foreach ($notifications as $index_of_notif => $notification) {
							foreach ($other_users as $index_of_user => $user_id) {
								if($index_of_notif === $index_of_user){
										$stmt = $conn->prepare("SELECT username from users where id = :user_id");
										$stmt->bindParam(':user_id', $user_id['sender']);
										if($stmt->execute())
											$other_username = $stmt->fetch(PDO::FETCH_ASSOC)['username'];
									foreach ($notification as $key => $value) {
										if($key ==='id'){
											$notification_id = $value;
										} else if ($key === 'description'){
											$notification_description = $value;
										} else if ($key !== 'is_clicked' and $key !== 'user'){
											echo '<a href="'."messages_user.php?user=".$other_username.'">'.$notification_description.'</a>';
										}else {
											$notification_is_clicked = $value;
										}
									}
								}
							}
						}
					 ?>
				    
		  		</div>
	  	</span>
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