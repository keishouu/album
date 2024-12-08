<?php  
require_once 'dbConfig.php';
require_once 'models.php';

if (isset($_POST['insertNewUserBtn'])) {
	$username = trim($_POST['username']);
	$first_name = trim($_POST['first_name']);
	$last_name = trim($_POST['last_name']);
	$password = trim($_POST['password']);
	$confirm_password = trim($_POST['confirm_password']);

	if (!empty($username) && !empty($first_name) && !empty($last_name) && !empty($password) && !empty($confirm_password)) {

		if ($password == $confirm_password) {

			$insertQuery = insertNewUser($pdo, $username, $first_name, $last_name, password_hash($password, PASSWORD_DEFAULT));
			$_SESSION['message'] = $insertQuery['message'];

			if ($insertQuery['status'] == '200') {
				$_SESSION['message'] = $insertQuery['message'];
				$_SESSION['status'] = $insertQuery['status'];
				header("Location: ../login.php");
			}

			else {
				$_SESSION['message'] = $insertQuery['message'];
				$_SESSION['status'] = $insertQuery['status'];
				header("Location: ../register.php");
			}

		}
		else {
			$_SESSION['message'] = "Please make sure both passwords are equal";
			$_SESSION['status'] = '400';
			header("Location: ../register.php");
		}

	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../register.php");
	}
}

if (isset($_POST['loginUserBtn'])) {
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	if (!empty($username) && !empty($password)) {

		$loginQuery = checkIfUserExists($pdo, $username);
		$userIDFromDB = $loginQuery['userInfoArray']['user_id'];
		$usernameFromDB = $loginQuery['userInfoArray']['username'];
		$passwordFromDB = $loginQuery['userInfoArray']['password'];

		if (password_verify($password, $passwordFromDB)) {
			$_SESSION['user_id'] = $userIDFromDB;
			$_SESSION['username'] = $usernameFromDB;
			header("Location: ../index.php");
		}

		else {
			$_SESSION['message'] = "Username/password invalid";
			$_SESSION['status'] = "400";
			header("Location: ../login.php");
		}
	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../register.php");
	}

}

if (isset($_GET['logoutUserBtn'])) {
	unset($_SESSION['user_id']);
	unset($_SESSION['username']);
	header("Location: ../login.php");
}


if (isset($_POST['insertPhotoBtn'])) {
    $photoDescription = trim($_POST['photoDescription']);
    $albumID = $_POST['albumID']; // Add a dropdown for albums in the form
    $userID = $_SESSION['user_id'];

    if (!empty($photoDescription) && !empty($albumID) && isset($_FILES['image'])) {
        $fileName = basename($_FILES['image']['name']);
        $targetDir = "../images/";
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $sql = "INSERT INTO Photos (AlbumID, PhotoURL, Caption) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$albumID, $fileName, $photoDescription])) {
                $_SESSION['message'] = "Photo uploaded successfully!";
            } else {
                $_SESSION['message'] = "Failed to upload photo.";
            }
        } else {
            $_SESSION['message'] = "Failed to move uploaded file.";
        }
        header("Location: ../index.php");
    } else {
        $_SESSION['message'] = "Please fill in all fields and select a file.";
        header("Location: ../index.php");
    }
}

if (isset($_POST['insertPhotoBtn'])) {
    $photoDescription = trim($_POST['photoDescription']);
    $albumID = $_POST['albumID']; // Add a dropdown for albums in the form
    $userID = $_SESSION['user_id'];

    if (!empty($photoDescription) && !empty($albumID) && isset($_FILES['image'])) {
        $fileName = basename($_FILES['image']['name']);
        $targetDir = "../images/";
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $sql = "INSERT INTO Photos (AlbumID, PhotoURL, Caption) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$albumID, $fileName, $photoDescription])) {
                $_SESSION['message'] = "Photo uploaded successfully!";
            } else {
                $_SESSION['message'] = "Failed to upload photo.";
            }
        } else {
            $_SESSION['message'] = "Failed to move uploaded file.";
        }
        header("Location: ../index.php");
    } else {
        $_SESSION['message'] = "Please fill in all fields and select a file.";
        header("Location: ../index.php");
    }
}


if (isset($_POST['deletePhotoBtn'])) {
	$photo_name = $_POST['photo_name'];
	$photo_id = $_POST['photo_id'];
	$deletePhoto = deletePhoto($pdo, $photo_id);

	if ($deletePhoto) {
		unlink("../images/".$photo_name);
		header("Location: ../index.php");
	}
       
}

if (isset($_POST['insertAlbumBtn'])) {
    $albumName = trim($_POST['albumName']);
    $albumDescription = trim($_POST['albumDescription']);
    $userID = $_SESSION['user_id'];

    if (!empty($albumName) && !empty($albumDescription)) {
        $sql = "INSERT INTO Albums (user_id, AlbumName, Description) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$userID, $albumName, $albumDescription])) {
            $_SESSION['message'] = "Album created successfully!";
        } else {
            $_SESSION['message'] = "Failed to create album.";
        }
        header("Location: ../editAlbum.php");
    } else {
        $_SESSION['message'] = "Please fill in all fields.";
        header("Location: ../index.php");
    }
}

if (isset($_POST['createAlbumBtn'])) {
    $albumName = trim($_POST['albumName']);
    $description = trim($_POST['description']);
    $userId = $_SESSION['user_id']; // Assuming the user is logged in

    if (!empty($albumName) && !empty($userId)) {
        $stmt = $pdo->prepare("
            INSERT INTO Albums (user_id, AlbumName, Description)
            VALUES (?, ?, ?)
        ");
        if ($stmt->execute([$userId, $albumName, $description])) {
            $_SESSION['message'] = "Album created successfully!";
            header("Location: ../index.php");
        } else {
            $_SESSION['message'] = "Failed to create album.";
            header("Location: ../createAlbum.php");
        }
    } else {
        $_SESSION['message'] = "Album name is required.";
        header("Location: ../createAlbum.php");
    }
}

// Handle album update
if (isset($_POST['updateAlbumBtn'])) {
    $albumID = $_POST['albumID'];
    $albumName = trim($_POST['albumName']);
    $albumDescription = trim($_POST['albumDescription']);

    if (!empty($albumName) && !empty($albumDescription)) {
        $sql = "UPDATE Albums SET AlbumName = ?, Description = ? WHERE AlbumID = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$albumName, $albumDescription, $albumID])) {
            $_SESSION['message'] = "Album updated successfully!";
        } else {
            $_SESSION['message'] = "Failed to update album.";
        }
        header("Location: ../index.php?albumID=" . $albumID);
    } else {
        $_SESSION['message'] = "Please fill in all fields.";
        header("Location: ../editAlbum.php?albumID=" . $albumID);
    }
}


// Handle photo upload
if (isset($_POST['insertPhotoBtn']) && isset($_FILES['images'])) {
    $albumID = $_POST['albumID'];  // Get album ID
    $photoCaptions = $_POST['photoCaption'];  // Get photo captions

    // Loop through the uploaded files
    foreach ($_FILES['images']['name'] as $key => $imageName) {
        $tmpName = $_FILES['images']['tmp_name'][$key];
        $fileExtension = pathinfo($imageName, PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $fileExtension;
        $targetDirectory = "../images/";  // Directory to upload images
        
        // Move the uploaded file to the target directory
        if (move_uploaded_file($tmpName, $targetDirectory . $fileName)) {
            // Insert the photo into the database
            $insertPhoto = $pdo->prepare("INSERT INTO Photos (AlbumID, PhotoURL, Caption) VALUES (?, ?, ?)");
            $insertPhoto->execute([$albumID, $fileName, $photoCaptions]);
        }
    }
    
    // Redirect to the album edit page after upload
    header("Location: ../editAlbum.php?albumID=" . $albumID);
    exit;
}

