<div class="navbar" style="text-align: center; margin-bottom: 50px;">
	<h1 style="color: white">Welcome to UCOSnaps, <span style="color: #466889 ;"><?php echo $_SESSION['username']; ?></span></h1>
	<a href="index.php">Home</a>
	<a href="yourProfile.php?username=<?php echo $_SESSION['username']; ?>">Your Profile</a>
	<a href="core/handleForms.php?logoutUserBtn=1">Logout</a>
</div>