<?php
$veritabaniSunucuAdi = "localhost";
$veritabaniKullaniciAdi = "root";
$veritabaniSifre = "";
$veritabaniAdi = "sinema";

$veritabaniBaglantisi = mysqli_connect($veritabaniSunucuAdi, $veritabaniKullaniciAdi, $veritabaniSifre, $veritabaniAdi);

if (!$veritabaniBaglantisi) {
    die("Bağlantı hatası: " . mysqli_connect_error());
}

class Salon {
    private $veritabaniBaglantisi;

    public function __construct($veritabaniBaglantisi) {
        $this->veritabaniBaglantisi = $veritabaniBaglantisi;
    }

    public function salonEkle($salonAdi) {
        $sql = "INSERT INTO salonlar (salonAdi) VALUES ('$salonAdi')";
        if (mysqli_query($this->veritabaniBaglantisi, $sql)) {
            return true;
        } else {
            return false;
        }
    }
}

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $salonAdi = $_POST['salonAdi'];
    $salon = new Salon($veritabaniBaglantisi);
    if ($salon->salonEkle($salonAdi)) {
        $mesaj = '<div class="alert alert-success text-center" role="alert">
                <strong>Salon Ekleme Başarılı,</strong></div>';
            header('Refresh:2; url=admin_panel.php');
    } else {
        $mesaj = "Salon ekleme işlemi başarısız: " . mysqli_error($veritabaniBaglantisi);
    }
}

mysqli_close($veritabaniBaglantisi);
?>
<!DOCTYPE html>
<html lang="tr-TR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sinema Bilet Otomasyonu - Salon Ekle</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        nav {
            background-color: #333;
            padding: 10px 0;
            text-align: center;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            padding: 0 10px;
        }

        form {
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .salonEkle {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button[type="submit"] {
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .mesaj {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .basarili {
            background-color: #d4edda;
            color: #155724;
        }

        .basarisiz {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <nav>
        <a href="admin_panel.php"><b>Panel</b></a>
    </nav>
    <form action="" method="post">
        <div class="salonEkle">
            <label for="salonAdi">Salon Adı:</label>
            <input type="text" name="salonAdi" id="salonAdi" required>
        </div>
        <div class="salonEkle">
            <button type="submit">Ekle</button>
        </div>
    </form>
    <?php if ($mesaj): ?>
        <div class="mesaj <?php echo strpos($mesaj, 'başarıyla') !== false ? 'basarili' : 'basarisiz'; ?>">
            <?php echo $mesaj; ?>
        </div>
    <?php endif; ?>
</body>
</html>
