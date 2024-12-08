<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}


if (!isset($_GET['albumID'])) {
    die("Album ID is required.");
}

$albumID = $_GET['albumID'];


$getAlbum = $pdo->prepare("SELECT AlbumName, Description, DateCreated, user_id FROM Albums WHERE AlbumID = ?");
$getAlbum->execute([$albumID]);
$album = $getAlbum->fetch();

if (!$album) {
    die("Album not found.");
}


$getPhotos = $pdo->prepare("SELECT PhotoURL, Caption FROM Photos WHERE AlbumID = ?");
$getPhotos->execute([$albumID]);
$photos = $getPhotos->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($album['AlbumName']); ?></title>
    
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
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
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


        .albumPostWrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 60px); 
        }

        .albumPost {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 60%;
        }


        .albumPost h2 {
            margin-bottom: 10px;
            color: #466889;
        }

        .albumPost p {
            margin-bottom: 10px;
        }

        .photo {
            display: inline-block;
            margin: 10px;
            text-align: center;
            width: calc(33.333% - 20px); 
            box-sizing: border-box;
        }

        .photo img {
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .photo p {
            margin-top: 5px;
        }

        
        footer {
            text-align: center;
            padding: 10px;
            margin-top: 20px;
            background-color: #0b1957;
            color: white;
        }
        </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="albumPostWrapper">
    <div class="albumPost">
        <h2><?php echo htmlspecialchars($album['AlbumName']); ?></h2>
        <p><i>Created on <?php echo htmlspecialchars($album['DateCreated']); ?></i></p>
        <p><?php echo htmlspecialchars($album['Description']); ?></p>


        <?php foreach ($photos as $photo): ?>
            <div class="photo" style="margin-top: 10px;">
                <img src="images/<?php echo htmlspecialchars($photo['PhotoURL']); ?>" alt="Album Photo" style="width: 100%;">
            </div>
        <?php endforeach; ?>
    </div>
</div>


</body>
</html>
