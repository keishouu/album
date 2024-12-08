<?php
require_once 'core/dbConfig.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


if (isset($_GET['albumID'])) {
    $albumID = $_GET['albumID'];


    $getAlbum = $pdo->prepare("SELECT AlbumID, AlbumName, Description, DateCreated, user_id FROM Albums WHERE AlbumID = ?");
    $getAlbum->execute([$albumID]);
    $album = $getAlbum->fetch();


    if (!$album || $album['user_id'] != $_SESSION['user_id']) {
        echo "You do not have permission to delete this album.";
        exit;
    }

    
    $getPhotos = $pdo->prepare("SELECT PhotoURL, Caption FROM Photos WHERE AlbumID = ?");
    $getPhotos->execute([$albumID]);
    $photos = $getPhotos->fetchAll();

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirmDelete'])) {
        try {
    
            $pdo->beginTransaction();

        
            $deletePhotos = $pdo->prepare("DELETE FROM Photos WHERE AlbumID = ?");
            $deletePhotos->execute([$albumID]);

            
            $deleteAlbum = $pdo->prepare("DELETE FROM Albums WHERE AlbumID = ?");
            $deleteAlbum->execute([$albumID]);

         
            $pdo->commit();

           
            header("Location: index.php?message=Album deleted successfully");
            exit;

        } catch (Exception $e) {
      
            $pdo->rollBack();
            echo "Error deleting album: " . $e->getMessage();
            exit;
        }
    }
} else {

    echo "No album specified to delete.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Deletion</title>
    <style>
 
        body {
            font-family: 'Arial', sans-serif;
            background-color: #fff; 
            color: #333;
            margin: 0;
            padding: 0;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .container {
            width: 80%;
            margin: 0 auto;
        }


        .navbar {
            background-color: #9AA6B2;
            color: #fff;
            padding: 15px;
            text-align: center;
        }
        .navbar a {
            color: #fff;
            margin: 0 15px;
            font-size: 16px;
        }
        .navbar a:hover {
            color: #466889;
        }

        
        .confirmation {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-top: 50px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
            margin: auto;
        }

        .albumDetails {
            width: 100%;
            max-width: 500px;
            border: none;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }

        .albumPhotos ul {
            list-style-type: none; 
            padding: 0;
            margin: 0; 
            display: flex;
            flex-wrap: wrap; 
            justify-content: center; 
            gap: 10px; 
        }

        .photoContainer {
            text-align: center;
            width: 120px; 
        }

        .photoContainer img {
            width: 100%; 
            height: auto;
            border-radius: 5px; 
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
        }

   
        form {
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #d16254; 
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #f4d1ff; 
        }

     
        p a {
            display: inline-block;
            padding: 12px;
            background-color: #0b1957;
            color: white;
            border-radius: 8px;
            margin-top: 10px;
        }

        p a:hover {
            color: #f4d1ff; 
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="confirmation">
        <h2>Are you sure you want to delete this album?</h2>

      
        <div class="albumDetails">
            <h3><?php echo htmlspecialchars($album['AlbumName']); ?></h3>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($album['Description'] ?? 'No description available'); ?></p>
            <p><strong>Created on:</strong> <?php echo htmlspecialchars($album['DateCreated']); ?></p>
            <p><strong>Photos:</strong></p>

         
            <div class="albumPhotos">
                <?php if ($photos): ?>
                    <ul>
                        <?php foreach ($photos as $photo): ?>
                            <li class="photoContainer">
                                <img src="images/<?php echo htmlspecialchars($photo['PhotoURL']); ?>" alt="Photo">
                                <p><?php echo htmlspecialchars($photo['Caption']); ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No photos in this album.</p>
                <?php endif; ?>
            </div>
        </div>

        <form action="" method="POST">
            <p>Are you sure you want to delete the album and all its photos?</p>
            <p>
                <button type="submit" name="confirmDelete" value="yes">Yes, Delete Album</button>
                <a href="index.php" style="width: 94%;">No, Go Back</a>
            </p>
        </form>
    </div>

</body>
</html>
