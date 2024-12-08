<?php  
require_once 'core/models.php'; 
require_once 'core/handleForms.php'; 
?>



<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<style>	
		body {
			font-family: 'Arial', sans-serif;
			display: flex;
			background-color: #f4d1ff; 
			color: #333;
			margin: 0;
			padding: 0;
			display: flex;
			justify-content: center;
			align-items: center;
			height: 100vh;
		}

		a {
			color: #5784e6;
			text-decoration: none;
		}

		a:hover {
			text-decoration: underline;
		}


		form {
			background-color: #fff;
			padding: 30px;
			border-radius: 10px;
			box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
			width: 100%;
			max-width: 400px;
			margin-top: 20px;
		}


		h1 {
			color: #0b1957; 
			text-align: center;
			font-size: 2rem;
			margin-bottom: 20px;
		}


		input[type="text"], input[type="password"] {
			width: 100%;
			padding: 12px;
			margin: 10px 0;
			border-radius: 8px;
			border: 1px solid #ddd;
			font-size: 1rem;
			box-sizing: border-box;
		}


		input[type="submit"] {
			width: 100%;
			padding: 12px;
			background-color: #5670bd; 
			color: white;
			border: none;
			border-radius: 8px;
			font-size: 1.1rem;
			cursor: pointer;
			transition: background-color 0.3s ease;
		}

		input[type="submit"]:hover {
			background-color: #f4d1ff;
		}


		h1.message {
			text-align: center;
			font-size: 1.2rem;
			margin: 20px 0;
		}

		h1.message.green {
			color: green;
		}

		h1.message.red {
			color: red;
		}


		p {
			text-align: center;
			font-size: 1rem;
		}

		p a {
			font-weight: bold;
		}

		
		.background{
			position: absolute;
			z-index: 0;
			top: 0;
			left: 0;
			width: 100%;
			height: 100vh;
			overflow: hidden;
		}

		.background img {
			width: 100%;
			object-fit: cover;
		}

	</style>
</head>
<body>
	<div class="background">
		<img src="assets\background.jpg" alt="">
	</div>
	<?php  
	if (isset($_SESSION['message']) && isset($_SESSION['status'])) {

		if ($_SESSION['status'] == "200") {
			echo "<h1 class='message green'>{$_SESSION['message']}</h1>";
		}

		else {
			echo "<h1 class='message red'>{$_SESSION['message']}</h1>";	
		}

	}
	unset($_SESSION['message']);
	unset($_SESSION['status']);
	?>

	<div class="login-container" style="position: relative;z-index: 2;">
		
		<form action="core/handleForms.php" method="POST">
			<h1>Login to UCOSnaps!</h1> 
			<p>
				<label for="username">Username</label>
				<input type="text" name="username">
			</p>
			<p>
				<label for="username">Password</label>
				<input type="password" name="password">
				<input type="submit" name="loginUserBtn" style="margin-top: 25px;">
			</p>
			<p>Don't have an account? You may register <a href="register.php">here</a></p>
		</form>
		
	</div>
</body>
</html>