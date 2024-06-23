<?php
session_start();

$sunucuAdi = "localhost";
$kullaniciAdi = "root";
$sifre = "";
$veritabaniAdi = "sinema";

try {
    $pdo = new PDO("mysql:host=$sunucuAdi;dbname=$veritabaniAdi", $kullaniciAdi, $sifre);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantısı başarısız: " . $e->getMessage());
}

class Kullanici {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function kullaniciBul($kullanici_id) {
        $sorgu = $this->pdo->prepare("SELECT * FROM kullanici_giris WHERE id = ?");
        $sorgu->execute([$kullanici_id]);
        return $sorgu->fetch(PDO::FETCH_ASSOC);
    }

    public function kullaniciGuncelle($kullanici_id, $yeni_adsoyad, $yeni_mail, $yeni_sifre = null) {
        if ($yeni_sifre) {
            $sorgu = $this->pdo->prepare("UPDATE kullanici_giris SET adsoyad = ?, mail = ?, sifre = ? WHERE id = ?");
            return $sorgu->execute([$yeni_adsoyad, $yeni_mail, $yeni_sifre, $kullanici_id]);
        } else {
            $sorgu = $this->pdo->prepare("UPDATE kullanici_giris SET adsoyad = ?, mail = ? WHERE id = ?");
            return $sorgu->execute([$yeni_adsoyad, $yeni_mail, $kullanici_id]);
        }
    }
}

$kullaniciNesne = new Kullanici($pdo);

if (isset($_GET['id'])) {
    $kullanici_id = $_GET['id'];
    $kullanici = $kullaniciNesne->kullaniciBul($kullanici_id);

    if (!$kullanici) {
        echo "Kullanıcı bulunamadı.";
        exit();
    }
} else {
    echo "Kullanıcı ID parametresi eksik.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $yeni_adsoyad = $_POST['yeni_adsoyad'];
    $yeni_mail = $_POST['yeni_mail'];
    $yeni_sifre = $_POST['yeni_sifre'];

    $guncelle_sonuc = $kullaniciNesne->kullaniciGuncelle($kullanici_id, $yeni_adsoyad, $yeni_mail, $yeni_sifre);

    if ($guncelle_sonuc) {
        echo '<div class="alert alert-success text-center" role="alert">
        <strong>Kullanıcı Güncelleme Başarılı</strong></div>';
        header('Refresh:2; admin_panel.php');
        exit();
    } else {
        $_SESSION['hata_mesaji'] = "Güncelleme sırasında bir hata oluştu.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Düzenle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbs5jQhjL6EfrG0tH9K1cE2tU5wAac3h/V3xUvLnE9Brx5yL94MPJ+axIF+Jmqs" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            text-align: center;
            color: #3498db;
            margin-bottom: 20px;
        }

        .form-container label {
            color: #555;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        .form-container input:focus {
            border-color: #3498db;
        }

        .form-container button {
            width: 100%;
            padding: 10px;
            background-color: #3498db;
            color: #fff;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            box-sizing: border-box;
            transition: background-color 0.3s;
        }

        .form-container button:hover {
            background-color: #2980b9;
        }

        .form-container .back-link {
            text-align: center;
            margin-top: 10px;
        }

        .form-container .back-link a {
            color: #555;
            text-decoration: none;
        }

        .alert {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container mt-5 form-container">
        <h2 class="mb-4">Kullanıcı Düzenle</h2>

        <form method="post" action="">
            <div class="mb-3">
                <label for="yeni_adsoyad" class="form-label">Ad Soyad:</label>
                <input type="text" class="form-control" name="yeni_adsoyad" value="<?php echo $kullanici['adsoyad']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="yeni_mail" class="form-label">E-posta:</label>
                <input type="email" class="form-control" name="yeni_mail" value="<?php echo $kullanici['mail']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="yeni_sifre" class="form-label">Yeni Şifre:</label>
                <input type="password" class="form-control" name="yeni_sifre">
            </div>

            <button type="submit" name="duzenle" class="btn btn-primary">Güncelle</button>
        </form>

        <div class="back-link">
            <a href="admin_panel.php">Geri</a>
        </div>
    </div>
</body>
</html>
