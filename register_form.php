<?php
	// Functie: programma login OOP 
    // Auteur: Rojvan Suleiman
	require_once('classes/User.php');

	$user = new User();
	$errors=[];

	// Is de register button aangeklikt?
	if(isset($_POST['register-btn'])){
		
		// Gegevens uit formulier halen
		$user->username = $_POST['username'];
		$user->password = $_POST['password'];   // <-- fixed from before

		// Validatie gegevens
		// Hoe???

		// Test of er geen errors zijn
		if(count($errors) == 0){
			// Register user
			$result = $user->registerUser($user->username, $user->password); // <-- fixed here
			
			// Controleer of het resultaat een foutmelding is of niet
			if (is_string($result)) {
				array_push($errors, $result);
			}
		}
		
		if(!empty($errors)){   // <-- safer check
			$message = "";
			foreach ($errors as $error) {
				$message .= $error . "\\n";
			}
			
			echo "
			<script>alert('" . $message . "')</script>
			<script>window.location = 'register_form.php'</script>";
		
		} else {
			echo "
				<script>alert('" . "User registerd" . "')</script>
				<script>window.location = 'login_form.php'</script>";
		}

	}
?>

<!DOCTYPE html>
<html lang="en">

<body>
	

		<h3>PHP - PDO Login and Registration</h3>
		<hr/>

			<form action="" method="POST">	
				<h4>Register here...</h4>
				<hr>
				
				<div>
					<label>Username</label>
					<input type="text"  name="username" />
				</div>
				<div >
					<label>Password</label>
					<input type="password"  name="password" />
				</div>
				<br />
				<div>
					<button type="submit" name="register-btn">Register</button>
				</div>
				<a href="index.php">Home</a>
			</form>


</body>
</html>
