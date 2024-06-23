<?php
session_start();

class Veritabani {
    private $host = "localhost";
    private $kullanici = "root";
    private $sifre = "";
    private $dbAdi = "sinema";
    private $baglanti;

    public function __construct() {
        try {
            $this->baglanti = new PDO("mysql:host=$this->host;dbname=$this->dbAdi", $this->kullanici, $this->sifre);
            $this->baglanti->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Bağlantı hatası: " . $e->getMessage());
        }
    }

    public function baglanti() {
        return $this->baglanti;
    }
}

class Kullanici {
    private $baglanti;

    public function __construct($baglanti) {
        $this->baglanti = $baglanti;
    }

    public function kullaniciVarMi($mail) {
        $sorgu = $this->baglanti->prepare('SELECT * FROM kullanici_giris WHERE mail = :mail');
        $sorgu->execute(['mail' => $mail]);
        return $sorgu->rowCount() > 0;
    }

    public function kullaniciEkle($adsoyad, $mail, $sifre) {
        $sorgu = $this->baglanti->prepare('INSERT INTO kullanici_giris (adsoyad, mail, sifre) VALUES (:adsoyad, :mail, :sifre)');
        return $sorgu->execute([
            'adsoyad' => $adsoyad,
            'mail' => $mail,
            'sifre' => $sifre
        ]);
    }
}

$mesaj = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kayitol'])) {
    $adsoyad = $_POST['adsoyad'];
    $mail = $_POST['mail'];
    $sifre = $_POST['sifre'];

    $veritabani = new Veritabani();
    $kullanici = new Kullanici($veritabani->baglanti());

    if ($kullanici->kullaniciVarMi($mail)) {
        $mesaj = '<div class="alert alert-danger text-center" role="alert">
            <strong>Bu e-posta adresi zaten kayıtlı!</strong></div>';
    } else {
        if ($kullanici->kullaniciEkle($adsoyad, $mail, $sifre)) {
            $mesaj = '<div class="alert alert-success text-center" role="alert">
                <strong>Kayıt Başarılı, Giriş Yapabilirsiniz</strong></div>';
            header('Refresh:2; url=giris.php');
        } else {
            $mesaj = '<div class="alert alert-danger text-center" role="alert">
                <strong>Kayıt İşlemi Başarısız</strong></div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol - Sinema Sitesi</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #343a40;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            max-width: 400px;
            width: 100%;
        }

        .card {
            background-color: #212529;
            border: none;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
        }

        .card-header {
            background-color: #343a40;
            border-bottom: 1px solid #495057;
        }

        .card-header h2 {
            color: #fff;
        }

        .card-body {
            padding: 30px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            width: 100%;
            margin-bottom: 10px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            width: 100%;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body class="bg-dark text-white">
    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                <h2>Kayıt Ol</h2>
            </div>
            <div class="card-body">
                <?php if ($mesaj) echo $mesaj; ?>
                <form method="post">
                    <div class="form-group">
                        <input type="text" class="form-control" name="adsoyad" placeholder="Ad Soyad" required>
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" name="mail" placeholder="E-posta" required>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="sifre" placeholder="Şifre" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" name="kayitol">Kayıt Ol</button>
                    </div>
                </form>
                <div class="text-center">
                    <a href="giris.php" class="btn btn-secondary">Giriş Yap</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
