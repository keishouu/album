<?php  
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$getAlbums = $pdo->prepare("
    SELECT Albums.AlbumID, Albums.AlbumName, Albums.Description, Albums.DateCreated, user_accounts.username, Albums.user_id
    FROM Albums
    LEFT JOIN user_accounts ON Albums.user_id = user_accounts.user_id
    ORDER BY Albums.DateCreated DESC
");
$getAlbums->execute();
$albums = $getAlbums->fetchAll();

$photosByAlbum = [];
foreach ($albums as $album) {
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
    <title>Album Feed</title>
    <link rel="stylesheet" href="styles/styles.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
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
.insertAlbumForm {
    width: 60%;
    display: flex;
    flex-direction: column;
    align-items: center;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 0px 15px 6px rgba(0, 0, 0, 0.1);
    margin: 30px auto;
}
.insertAlbumForm input, .insertAlbumForm textarea {
    width: 100%;
    max-width: 600px;
    padding: 10px;
    margin: 10px 0;
    border-radius: 8px;
    border: 1px solid #ddd;
}
.insertAlbumForm input[type="submit"] {
    background-color: #9AA6B2;
    color: white;
    cursor: pointer;
}
.insertAlbumForm input[type="submit"]:hover {
    background-color: #9AA6B2;
}
.gallery .row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center;
}
.gallery .col-lg-4, .gallery .col-md-6 {
    width: 100%;
    max-width: 350px;
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.gallery .col-lg-4:hover, .gallery .col-md-6:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}
.gallery img {
    width: 100%;
    max-height: 250px;
    object-fit: cover;
    border-radius: 8px;
}
.albumPost {
    width: 60%;
    margin: 20px auto;
    background-color: #fff;
    border: 1px solid #ddd;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
.albumPost h2 {
    font-size: 1.8rem;
    color: #5784e6;
    margin-bottom: 10px;
}
.albumPost p {
    font-size: 1rem;
    color: #555;
    margin-bottom: 10px;
}
.albumPost .see-more a {
    color: #fa9ebc;
    font-size: 1rem;
}
.album-actions a {
    color: #5784e6;
    font-size: 1rem;
    margin-right: 15px;
    transition: color 0.3s ease;
}
.album-actions a:hover {
    color: #9AA6B2;
}
.see-more {
    text-align: right;
    margin-top: 10px;
}
footer {
    background-color: #9AA6B2;
    color: #fff;
    padding: 20px;
    text-align: center;
    font-size: 14px;
    position: fixed;
    width: 100%;
    bottom: 0;
}
@media (max-width: 768px) {
    .gallery .col-lg-4, .gallery .col-md-6 {
        width: 100%;
        margin-bottom: 20px;
    }
    .insertAlbumForm {
        width: 90%;
    }
    .albumPost {
        width: 90%;
    }
}
@media (max-width: 480px) {
    .navbar a {
        font-size: 14px;
    }
    .insertAlbumForm input, .insertAlbumForm textarea {
        width: 100%;
    }
}
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="insertAlbumForm">
        <form action="core/handleForms.php" method="POST">
            <p>
                <label for="albumName">Album Name</label>
                <input type="text" name="albumName" required>
            </p>
            <p>
                <label for="albumDescription">Album Description</label>
                <textarea name="albumDescription" required></textarea>
            </p>
            <p>
                <input type="submit" name="insertAlbumBtn" value="Create Album">
            </p>
        </form>
    </div>

    <?php foreach ($albums as $album): ?>
        <div class="albumPost">
            <p>
                <h2 href="profile.php?userID=<?php echo $album['user_id']; ?>" style="color: #9AA6B2">
                    <?php echo htmlspecialchars($album['username']); ?>
                </h2>
                <span><?php echo htmlspecialchars($album['DateCreated']); ?></span>
            </p>
            <h3 style="color:#466889;"><?php echo htmlspecialchars($album['AlbumName']); ?></h3>
            <p> <?php echo htmlspecialchars($album['Description'] ?? 'No description available'); ?></p>
            <div class="gallery">
                <div class="row">
                    <?php
                    $photoCount = count($photosByAlbum[$album['AlbumID']]);
                    $photosToShow = array_slice($photosByAlbum[$album['AlbumID']], 0, 3);
                    ?>
                    <?php foreach ($photosToShow as $photo): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <img src="images/<?php echo htmlspecialchars($photo['PhotoURL']); ?>" class="shadow-1-strong rounded mb-4" alt="<?php echo htmlspecialchars($photo['Caption'] ?? 'No caption available'); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php if ($photoCount > 3): ?>
                <div class="see-more">
                    <a href="viewAlbum.php?albumID=<?php echo $album['AlbumID']; ?>" style="color:#9AA6B2;">See more</a>
                </div>
            <?php endif; ?>
            <?php if ($album['user_id'] == $_SESSION['user_id']): ?>
                <div class="album-actions">
                    <a href="editAlbum.php?albumID=<?php echo $album['AlbumID']; ?>">Edit Album</a> |
                    <a href="deleteAlbum.php?albumID=<?php echo $album['AlbumID']; ?>">Delete</a>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</body>
</html>
