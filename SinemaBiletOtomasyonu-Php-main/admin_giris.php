<?php
session_start();

class KullaniciGiris {
    private $veritabani;

    public function __construct($servername, $username, $password, $dbname) {
        $this->veritabaniBaglantisi($servername, $username, $password, $dbname);
    }

    private function veritabaniBaglantisi($servername, $username, $password, $dbname) {
        try {
            $this->veritabani = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $this->veritabani->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Bağlantı hatası: " . $e->getMessage();
            exit; 
        }
    }

    public function girisKontrol($kullanici_adi, $parola) {
        $sorgu = $this->veritabani->prepare('SELECT * FROM admins WHERE kullanici_adi=:kullanici_adi AND parola=:parola');
        $sorgu->execute([
            'kullanici_adi' => $kullanici_adi,
            'parola' => $parola
        ]);

        return $sorgu->rowCount() === 1;
    }
}

$kullaniciGiris = new KullaniciGiris("localhost", "root", "", "sinema");

if (isset($_POST['giris'])) {
    $kullanici_adi = $_POST['kullanici_adi'];
    $parola = $_POST['parola'];

    if ($kullaniciGiris->girisKontrol($kullanici_adi, $parola)) {
        $_SESSION['kullanici_adi'] = $kullanici_adi;
        echo '<div class="alert alert-primary text-center" role="alert">
                <strong>Giriş Başarılı</strong>
              </div>';
        header('Refresh:2; admin_panel.php');
        exit;
    } else {
        echo '<div class="alert alert-danger text-center" role="alert">
                <strong>Giriş Bilgileri Hatalı</strong>
              </div>';
        header('Refresh:2; admin_giris.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Otopark Otomasyonu - Yönetici Giriş</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #343a40;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .card {
            padding: 20px;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #212529;
            max-width: 400px;
            width: 100%;
        }

        .card h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #fff;
            text-align: center;
        }

        .form-control {
            margin-bottom: 15px;
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            transition: border-color 0.3s ease-in-out;
        }

        .form-control:focus {
            border-color: #007bff;
            outline: none;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 12px;
            border-radius: 5px;
            color: #fff;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease-in-out;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-danger {
            background-color: #dc3545;
            border: none;
            padding: 12px;
            border-radius: 5px;
            color: #fff;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease-in-out;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .alert {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
        }

        .alert-primary {
            background-color: #cce5ff;
            border-color: #b8daff;
            color: #004085;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
    </style>
</head>

<body>
    <div class="card">
        <h2>YÖNETİCİ GİRİŞ</h2>
        <form action="admin_giris.php" method="post">
            <input type="text" name="kullanici_adi" class="form-control" placeholder="Kullanıcı adınızı giriniz" required>
            <input type="password" name="parola" class="form-control" placeholder="Parola Giriniz" required>
            <input type="submit" class="btn btn-primary" name="giris" value="GİRİŞ YAP">
            <a href="giris.php" class="btn btn-danger mt-3">GERİ</a>
        </form>
    </div>

    <!-- Optional JavaScript -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>
