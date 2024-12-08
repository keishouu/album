<?php
require_once 'core/dbConfig.php'; 


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");  
    exit();
}


$getAlbums = $pdo->prepare("SELECT AlbumID, AlbumName, Description, DateCreated FROM Albums WHERE user_id = ? ORDER BY DateCreated DESC");
$getAlbums->execute([$_SESSION['user_id']]);
$albums = $getAlbums->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile - Albums</title>
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


.profile-page {
    width: 80%;
    margin: 40px auto;
}

.profile-page h1 {
    font-size: 2rem;
    color: #466889; 
    text-align: center;
    margin-bottom: 20px;
}


.albums-list {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
}

.album {
    width: 30%;
    background-color: #fff;
    border: none;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 0px 15px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    flex-direction: column; 
    justify-content: center; 
    align-items: center; 
    text-align: center; 
}

.album:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}

.album h2 {
    font-size: 1.5rem;
    color: #0b1957; 
}

.album p {
    color: #777;
}

.album .album-photo img {
    width: 100%;
    max-width: 280px;
    object-fit: cover;
    border-radius: 8px;
}

.album a {
    color: #5784e6; 
    text-decoration: none;
    margin-right: 10px;
}

.album a:hover {
    text-decoration: underline;
}


.profile-page p {
    text-align: center;
    color: #777;
}

footer {
    background-color: #0b1957;
    color: #fff;
    padding: 20px;
    text-align: center;
    font-size: 14px;
    position: fixed;
    width: 100%;
    bottom: 0;
}

@media (max-width: 768px) {
    .album {
        width: 100%;
    }

    .profile-page {
        width: 90%;
    }
}

    </style>

</head>
<body>
    <?php include 'navbar.php'; ?>  

    <div class="profile-page">
        <h1>Your Albums</h1>

      
        <?php if (count($albums) > 0): ?>
            <div class="albums-list">
                <?php foreach ($albums as $album): ?>
                    <div class="album" style=" padding: 20px; margin: 20px;">
                        <h2 style="color:#466889;"><?php echo htmlspecialchars($album['AlbumName']); ?></h2>
                        <p><?php echo htmlspecialchars($album['Description'] ?? 'No description available'); ?></p>
                        <p><?php echo htmlspecialchars($album['DateCreated']); ?></p>

                        
                        <?php
                        $getFirstPhoto = $pdo->prepare("SELECT PhotoURL FROM Photos WHERE AlbumID = ? LIMIT 1");
                        $getFirstPhoto->execute([$album['AlbumID']]);
                        $firstPhoto = $getFirstPhoto->fetch();
                        ?>

                        <?php if ($firstPhoto): ?>
                            <div class="album-photo" style="margin-top: 10px;">
                                <img src="images/<?php echo htmlspecialchars($firstPhoto['PhotoURL']); ?>" alt="Album Photo" style="width: 100%; max-width: 300px;">
                            </div>
                        <?php else: ?>
                            <p>No photos in this album yet.</p>
                        <?php endif; ?>

                        <a href="viewAlbum.php?albumID=<?php echo $album['AlbumID']; ?>">View Album</a>| 
                        <a href="editAlbum.php?albumID=<?php echo $album['AlbumID']; ?>">Edit Album</a>| 
                        <a href="deleteAlbum.php?albumID=<?php echo $album['AlbumID']; ?>">Delete Album</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>You have not created any albums yet.</p>
        <?php endif; ?>

        <p><a href="index.php">Back to Home</a></p>
    </div>

</body>
</html>
