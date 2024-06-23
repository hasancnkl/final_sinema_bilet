<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sinema";


$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Veritabanı bağlantı hatası: " . mysqli_connect_error());
}

include 'api.php';
$sayfaid = isset($_GET["sayfa"]) ? $_GET["sayfa"] : 1;

$kategori = isset($_GET["kategori"]) ? $_GET["kategori"] : 0;

if ($sayfaid < 1) { $sayfaid = 1; }
if ($sayfaid >= 500) { $sayfaid = 500; }
if (!is_numeric($sayfaid)) { $sayfaid = 1; }
if (!is_numeric($kategori)) { $kategori = 0; }

if ($kategori == 0) { $kategoriAdi = "Tümü"; }

$url = "https://api.themoviedb.org/3/genre/movie/list?api_key=$api&language=tr-TR";
$json = file_get_contents($url);
$json = json_decode($json);
$kategoriler = $json->genres;
$kategoriSayi = count($kategoriler);

for ($i = 0; $i < $kategoriSayi; $i++) {
    $katid = $json->genres[$i]->id;
    $katAdi = $json->genres[$i]->name;

    if ($katid == $kategori) {
        $kategoriAdi = $katAdi;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Film Rehberi - 2</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g==" crossorigin="anonymous" />
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Stil tanımlamaları */
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            margin: 0;
            padding: 0;
        }
        .nav-wrapper {
            background-color: #333;
            padding: 10px 0;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 0 20px;
        }
        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-menu {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .nav-menu li {
            margin-right: 20px;
        }
        .nav-menu li a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
        }
        .nav-menu li a:hover {
            color: #ffcc00;
        }
        .hamburger-menu {
            display: none;
            cursor: pointer;
        }
        .hamburger-menu .hamburger {
            width: 30px;
            height: 3px;
            background-color: #fff;
            margin: 5px 0;
        }
        .section {
            padding: 20px 0;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 0 20px;
        }
        .film-tablo {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-top: 20px;
        }
        .film-tablo .film-item {
            flex-basis: calc(25% - 20px);
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            transition: transform 0.3s ease;
        }
        .film-tablo .film-item:hover {
            transform: translateY(-5px);
        }
        .film-tablo .film-item img {
            width: 100%;
            height: auto;
        }
        .film-tablo .film-item .movie-item-content {
            padding: 10px;
        }
        .film-tablo .film-item .movie-item-title {
            font-size: 18px;
            margin-bottom: 5px;
            color: #333;
        }
        .film-tablo .film-item .movie-info {
            display: flex;
            align-items: center;
            margin-top: 5px;
            font-size: 14px;
            color: #777;
        }
        .film-tablo .film-item .movie-info i {
            margin-right: 5px;
        }
        .ileri-geri {
            text-align: center;
            margin-top: 20px;
        }
        .ileri-geri a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .ileri-geri a:hover {
            background-color: #ffcc00;
        }
    </style>
</head>
<body>
   
    <div class="nav-wrapper">
        <div class="container">
            <div class="nav">
                <ul class="nav-menu" id="nav-menu">
                    <li><a href="admin_panel.php">Admin Panel</a></li>
                </ul>
                <div class="hamburger-menu" id="hamburger-menu">
                    <div class="hamburger"></div>
                </div>
            </div>
        </div>
    </div>
    
   
    <div class="section">
        <div class="container">
            <div class="film-tablo">
                <?php 
                if ($kategori == 0) {
                    $url = "https://api.themoviedb.org/3/discover/movie?api_key=$api&language=tr-TR&sort_by=popularity.desc&page=$sayfaid";
                } else {
                    $url = "https://api.themoviedb.org/3/discover/movie?api_key=$api&language=tr-TR&sort_by=popularity.desc&page=$sayfaid&with_genres=$kategori";
                }

                $json = file_get_contents($url);
                $json = json_decode($json);
                $filmler = $json->results;
                
                $FilmSayi = count($filmler);
                for ($i = 0; $i < $FilmSayi; $i++) {
                    $baslik = $json->results[$i]->title;
                    $poster = $json->results[$i]->poster_path;
                    $aciklama = $json->results[$i]->overview;
                    $idsi = $json->results[$i]->id;
                    $url2 = "https://api.themoviedb.org/3/movie/$idsi?api_key=$api&language=tr-TR";
                    $json2 = file_get_contents($url2);
                    $json2 = json_decode($json2);
                    $puan = $json2->vote_average;
                    $puan = substr($puan, 0, 3);
                    $sure = $json2->runtime;
                    $imdb = $json2->imdb_id;
                    $turler = $json2->genres;
                    $tursayi = count($turler);
                    $turBirles = "";
                    for ($x = 0; $x < $tursayi; $x++) {
                        $turum = $json2->genres[$x]->name;
                        if (($x + 1) == $tursayi) {
                            $turBirles .= $turum;
                        } else {
                            $turBirles .= $turum . ", ";
                        }
                    }
                ?>
                
                <div class="film-item">
                    <a href="detay?id=<?php echo $idsi; ?>&tip=film">
                       
                        <img src="https://www.themoviedb.org/t/p/original/<?php echo $poster; ?>" alt="<?php echo $baslik; ?>">
                       
                        <div class="movie-item-content">
                            <div class="movie-item-title"><?php echo $baslik; ?></div>
                            <div class="movie-info">
                                <i class="bx bxs-star"></i>
                                <span><?php echo $puan; ?></span>
                            </div>
                            <div class="movie-info">
                                <i class="bx bxs-time"></i>
                                <span><?php echo $sure; ?> dk.</span>
                            </div>
                            <div class="movie-info">
                                <span><?php echo $turBirles; ?></span>
                            </div>
                        </div>
                    </a>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

   
    

    <!-- Gerekli JavaScript dosyaları -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw==" crossorigin="anonymous"></script>
    <script src="js/app.js"></script>

</body>
</html>
