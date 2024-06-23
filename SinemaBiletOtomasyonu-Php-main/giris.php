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

    public function girisYap($mail, $sifre) {
        $sorgu = $this->baglanti->prepare('SELECT * FROM kullanici_giris WHERE mail = :mail AND sifre = :sifre');
        $sorgu->bindParam(':mail', $mail);
        $sorgu->bindParam(':sifre', $sifre);
        $sorgu->execute();

        return $sorgu->fetch(PDO::FETCH_ASSOC);
    }
}

$mesaj = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['giris'])) {
    $mail = $_POST['mail'];
    $sifre = $_POST['sifre'];

    $veritabani = new Veritabani();
    $kullanici = new Kullanici($veritabani->baglanti());

    $sonuc = $kullanici->girisYap($mail, $sifre);

    if ($sonuc) {
        $_SESSION['mail'] = $mail;
        $_SESSION["musteri_id"] = $sonuc["id"];
        $mesaj = '<div class="alert alert-primary text-center" role="alert"><strong>Giriş Başarılı</strong></div>';
        header('Refresh:2; index.php');
    } else {
        $mesaj = '<div class="alert alert-danger text-center" role="alert"><strong>Giriş Bilgileri Hatalı</strong></div>';
        header('Refresh:2; giris.php');
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Sinema Bilet Otomasyonu</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" crossorigin="anonymous">
    <style>
        body {
            background-color: #343a40;
            color: #fff;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            margin-top: 50px;
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

        .btn-primary,
        .btn-secondary,
        .btn-danger {
            width: 100%;
            margin-bottom: 10px;
            font-size: 16px;
            padding: 10px 20px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-secondary:hover {
            background-color: #545b62;
            border-color: #545b62;
        }

        .btn-danger:hover {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                <h2>Sinema Bilet Otomasyonu</h2>
            </div>
            <div class="card-body">
                <?php if ($mesaj): ?>
                    <?php echo $mesaj; ?>
                <?php endif; ?>
                <div class="text-center">
                    <h4>Giriş Yapınız</h4>
                </div>
                <form action="" method="post">
                    <div class="form-group">
                        <input type="email" name="mail" class="form-control" placeholder="Mail Giriniz" required>
                        <input type="password" name="sifre" class="form-control mt-3" placeholder="Şifre Giriniz" required>
                    </div>
                    <div class="text-center">
                        <input type="submit" class="btn btn-primary" name="giris" value="GİRİŞ YAP">
                        <a href="kayit.php" class="btn btn-danger">Kayıt Ol</a>
                        <a href="admin_giris.php" class="btn btn-secondary mt-2">Panel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>
