<?php

class SinemaRehberi {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "sinema";
    private $conn;
    private $api;

    public function __construct($api) {
        $this->conn = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);
        if (!$this->conn) {
            die("Veritabanı bağlantı hatası: " . mysqli_connect_error());
        }
        $this->api = $api;
    }

    public function __destruct() {
        mysqli_close($this->conn);
    }

    public function getKategoriAdi($kategoriId) {
        $url = "https://api.themoviedb.org/3/genre/movie/list?api_key={$this->api}&language=tr-TR";
        $json = file_get_contents($url);
        $kategoriler = json_decode($json)->genres;

        foreach ($kategoriler as $kategori) {
            if ($kategori->id == $kategoriId) {
                return $kategori->name;
            }
        }

        return "Tümü";
    }

    public function getFilmler($sayfa, $kategori) {
        $url = "https://api.themoviedb.org/3/discover/movie?api_key={$this->api}&language=tr-TR&sort_by=popularity.desc&page={$sayfa}";
        if ($kategori != 0) {
            $url .= "&with_genres={$kategori}";
        }
        $json = file_get_contents($url);
        return json_decode($json)->results;
    }

    public function getFilmDetaylari($filmId) {
        $url = "https://api.themoviedb.org/3/movie/{$filmId}?api_key={$this->api}&language=tr-TR";
        return json_decode(file_get_contents($url));
    }

    public function renderFilmler($filmler) {
        foreach ($filmler as $film) {
            $baslik = mysqli_real_escape_string($this->conn, $film->title);
            $poster = mysqli_real_escape_string($this->conn, $film->poster_path);
            $detaylar = $this->getFilmDetaylari($film->id);
            $puan = substr($detaylar->vote_average, 0, 3);
            $sure = $detaylar->runtime;
            $turBirles = implode(", ", array_map(function($tur) { return $tur->name; }, $detaylar->genres));

            echo "<div class='film-item'>
                    <a href='detay?id={$film->id}&tip=film'>
                        <img src='https://www.themoviedb.org/t/p/original/{$poster}' alt='{$baslik}'>
                        <div class='movie-item-content'>
                            <div class='movie-item-title'>{$baslik}</div>
                            <div class='movie-info'>
                                <i class='bx bxs-star'></i>
                                <span>{$puan}</span>
                            </div>
                            <div class='movie-info'>
                                <i class='bx bxs-time'></i>
                                <span>{$sure} dk.</span>
                            </div>
                            <div class='movie-info'>
                                <span>{$turBirles}</span>
                            </div>
                        </div>
                    </a>
                </div>";
        }
    }
}


include 'api.php';

$sayfa = isset($_GET["sayfa"]) ? max(1, min(500, intval($_GET["sayfa"]))) : 1;
$kategori = isset($_GET["kategori"]) ? intval($_GET["kategori"]) : 0;

$sinemaRehberi = new SinemaRehberi($api);
$kategoriAdi = $sinemaRehberi->getKategoriAdi($kategori);
$filmler = $sinemaRehberi->getFilmler($sayfa, $kategori);

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Film Rehberi - 2</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
       
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
        nav {
            background-color: #333;
            padding: 10px 0;
            text-align: center;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        nav a:hover {
            background-color: #555;
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
<nav>
    <a href="index.php"><b>Anasayfa</b></a>
    <a href="filmler.php"><b>Vizyondakiler</b></a>
    <a href="biletlerim.php"><b>Biletlerim</b></a>
    <a href="iletisim.php"><b>İletişim</b></a>
    <a href="hakkimizda.php"><b>Hakkımızda</b></a>
    <a href="giris.php"><b>Çıkış Yap</b></a>
</nav>

<div class="section">
    <div class="container">
        <div class="film-tablo">
            <?php $sinemaRehberi->renderFilmler($filmler); ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="js/app.js"></script>

</body>
</html>
