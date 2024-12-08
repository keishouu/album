<?php 
require_once 'core/dbConfig.php';  


if (!isset($_SESSION['username'])) {
    header("Location: login.php");  
    exit();
}


$userID = isset($_GET['userID']) ? $_GET['userID'] : null;
if (!$userID) {
    header("Location: index.php");  
    exit();
}


$getUserProfile = $pdo->prepare("SELECT username FROM user_accounts WHERE user_id = ?");
$getUserProfile->execute([$userID]);
$userProfile = $getUserProfile->fetch();


$getUserAlbums = $pdo->prepare("SELECT AlbumID, AlbumName, Description, DateCreated FROM Albums WHERE user_id = ? ORDER BY DateCreated DESC");
$getUserAlbums->execute([$userID]);
$userAlbums = $getUserAlbums->fetchAll();


$photosByAlbum = [];
foreach ($userAlbums as $album) {
    $albumID = $album['AlbumID'];
    $getPhotos = $pdo->prepare("SELECT PhotoURL, Caption FROM Photos WHERE AlbumID = ?");
    $getPhotos->execute([$albumID]);
    $photosByAlbum[$albumID] = $getPhotos->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($userProfile['username']); ?>'s Profile</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>  

    <div class="profile-page">
        <h1><?php echo htmlspecialchars($userProfile['username']); ?>'s Profile</h1>

       
        <h2>Albums</h2>
        <?php if (count($userAlbums) > 0): ?>
            <div class="albums-list">
                <?php foreach ($userAlbums as $album): ?>
                    <div class="album" style="border: 1px solid gray; padding: 20px; margin: 20px;">
                        <h3><?php echo htmlspecialchars($album['AlbumName']); ?></h3>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($album['Description'] ?? 'No description available'); ?></p>
                        <p><strong>Created on:</strong> <?php echo htmlspecialchars($album['DateCreated']); ?></p>

                       
                        <?php
                        $photoCount = count($photosByAlbum[$album['AlbumID']]);
                        $photosToShow = array_slice($photosByAlbum[$album['AlbumID']], 0, 3);
                        ?>
                        <?php foreach ($photosToShow as $photo): ?>
                            <div class="photo" style="margin-top: 10px;">
                                <img src="images/<?php echo htmlspecialchars($photo['PhotoURL']); ?>" alt="Album Photo" style="width: 100px; height: 100px; object-fit: cover;">
                                <p><?php echo htmlspecialchars($photo['Caption'] ?? 'No caption available'); ?></p>
                            </div>
                        <?php endforeach; ?>

                        
                        <?php if ($photoCount > 3): ?>
                            <a href="viewAlbum.php?albumID=<?php echo $album['AlbumID']; ?>">See more photos</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>This user has not created any albums yet.</p>
        <?php endif; ?>

    
        <p><a href="index.php">Back to Home</a></p>
    </div>

</body>
</html>
