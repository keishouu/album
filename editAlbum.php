<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 


if (!isset($_GET['albumID'])) {
    header("Location: index.php");
    exit;
}


$albumID = $_GET['albumID'];
$getAlbumDetails = $pdo->prepare("SELECT Albums.AlbumID, Albums.AlbumName, Albums.Description, Albums.DateCreated, 
                                      user_accounts.username, Albums.user_id as album_user_id
                                   FROM Albums
                                   LEFT JOIN user_accounts ON Albums.user_id = user_accounts.user_id
                                   WHERE Albums.AlbumID = ?");
$getAlbumDetails->execute([$albumID]);
$album = $getAlbumDetails->fetch();


$getAlbumPhotos = $pdo->prepare("SELECT PhotoID, PhotoURL, Caption FROM Photos WHERE AlbumID = ?");
$getAlbumPhotos->execute([$albumID]);
$photos = $getAlbumPhotos->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Album</title>
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
            width: 100%;
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
           
        .editAlbumFormContainer {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            width: 100%;
        }

        .editAlbumFormCard {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            display: flex;
            gap: 20px; 
        }

        .editAlbumForm, .insertPhotoForm {
            width: 100%;
            max-width: 380px; 
        }

        h1 {
            color: #9AA6B2; 
            text-align: center;
            font-size: 2rem;
            margin-bottom: 20px;
        }

   
        input[type="text"], input[type="password"], textarea {
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
            background-color: #9AA6B2; 
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


    .albumPhotos, .photoGallery {
        display: flex;
        justify-content: center; 
        align-items: center;     
        flex-wrap: wrap;
        margin-top: 20px;
        width: 100%;             
        max-width: 900px;        
        margin: 0 auto;          
    }

        .photoContainer {
            margin: 10px;
            text-align: center;
        }

        .photoContainer img {
            width: 200px;
            height: auto;
        }

     
        .insertPhotoForm {
            margin-top: 30px;
            width: 100%;
            max-width: 400px;
        }

        .insertPhotoForm input[type="file"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-size: 1rem;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">

        <div class="editAlbumFormContainer">

    <div class="editAlbumFormCard">

        <div class="editAlbumForm">
            <h1>Edit Album</h1>
            <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="albumID" value="<?php echo htmlspecialchars($album['AlbumID']); ?>">

                <p>
                    <label for="albumName">Album Name</label>
                    <input type="text" name="albumName" value="<?php echo htmlspecialchars($album['AlbumName']); ?>" required>
                </p>
                <p>
                    <label for="albumDescription">Album Description</label>
                    <textarea name="albumDescription" required><?php echo htmlspecialchars($album['Description']); ?></textarea>
                </p>
                <p>
                    <input type="submit" name="updateAlbumBtn" value="Update Album">
                </p>
            </form>
        </div>

 
        <div class="insertPhotoForm">
            <h1>Upload Photos</h1>
            <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="albumID" value="<?php echo htmlspecialchars($album['AlbumID']); ?>">
                <p>
                    <label for="images">Upload Photos</label>
                    <input type="file" name="images[]" multiple required>
                </p>
                <p>
                    <input type="submit" name="insertPhotoBtn" value="Upload Photos">
                </p>
            </form>
        </div>
    </div>
</div>



        <div class="albumPhotos">
            <h3>Existing Photos</h3>
            <?php if (count($photos) > 0): ?>
                <div class="photoGallery">
                    <?php foreach ($photos as $photo): ?>
                        <div class="photoContainer">
                            <img src="images/<?php echo htmlspecialchars($photo['PhotoURL']); ?>" alt="Album Photo">
                            <p><?php echo htmlspecialchars($photo['Caption'] ?? ''); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No photos uploaded yet.</p>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>
