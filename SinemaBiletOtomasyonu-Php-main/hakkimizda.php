<?php

$sunucuAdi = "localhost";
$kullaniciAdi = "root";
$sifre = "";
$veritabaniAdi = "sinema";

try {
    $pdo = new PDO("mysql:host={$sunucuAdi};dbname={$veritabaniAdi}", $kullaniciAdi, $sifre);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantısı başarısız: " . $e->getMessage());
}

class Sinema {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function hakkimizdaBilgileriniGetir() {
        $sql = "SELECT * FROM hakkimizda";
        try {
            $stmt = $this->pdo->query($sql);
            if ($stmt->rowCount() > 0) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        } catch (PDOException $e) {
            die("Veri çekme hatası: " . $e->getMessage());
        }
    }
}

$sinema = new Sinema($pdo);
$hakkimizdaVerileri = $sinema->hakkimizdaBilgileriniGetir();

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hakkımızda</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="admin.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #495057;
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
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        nav a:hover {
            background-color: #555;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h2,
        h3 {
            color: #007bff;
            margin-bottom: 20px;
        }

        p {
            color: #6c757d;
            line-height: 1.6;
        }

        strong {
            color: #343a40;
        }

        .contact-info {
            margin-top: 30px;
            border-top: 1px solid #ced4da;
            padding-top: 15px;
        }

        .contact-info strong {
            display: block;
            margin-bottom: 5px;
            color: #343a40;
        }

        .contact-button {
            margin-top: 15px;
        }

        .btn {
            padding: 10px 20px;
            background-color: #dc3545;
            color: #fff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #c82333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ced4da;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
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

<div class="container mt-5">
    <?php if (count($hakkimizdaVerileri) > 0): ?>
        <?php foreach ($hakkimizdaVerileri as $satir): ?>
            <div class="jumbotron text-center">
                <h2 class="display-4 font-weight-bold"><?= htmlspecialchars($satir["firma_adi"]) ?></h2>
                <p class="lead"><?= htmlspecialchars($satir["hosgeldiniz_baslik"]) ?></p>
                <p class="lead"><?= htmlspecialchars($satir["hosgeldiniz_aciklama"]) ?></p>
            </div>

            <div class="row mt-4">
                <div class="col-md-6 offset-md-3">
                    <h3 class="text-primary text-center font-weight-bold"><?= htmlspecialchars($satir["vizyon_baslik"]) ?></h3>
                    <p class="text-center"><?= htmlspecialchars($satir["vizyon_aciklama"]) ?></p>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6 offset-md-3">
                    <h3 class="text-primary text-center font-weight-bold"><?= htmlspecialchars($satir["misyon_baslik"]) ?></h3>
                    <p class="text-center"><?= htmlspecialchars($satir["misyon_aciklama"]) ?></p>
                </div>
            </div>

            <div class="contact-info mt-4 text-center">
                <h3 class="text-primary font-weight-bold">İletişim</h3>
                <table class="table table-bordered">
                    <tr>
                        <th class="text-center">E-posta</th>
                        <td><?= htmlspecialchars($satir["iletisim_eposta"]) ?></td>
                    </tr>
                    <tr>
                        <th class="text-center">Telefon</th>
                        <td><?= htmlspecialchars($satir["iletisim_telefon"]) ?></td>
                    </tr>
                    <tr>
                        <th class="text-center">Adres</th>
                        <td><?= htmlspecialchars($satir["iletisim_adres"]) ?></td>
                    </tr>
                </table>
            </div>

            <div class="contact-button mt-4 text-center">
                <a href="iletisim.php" class="btn btn-primary">İletişim Sayfası</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Veri bulunamadı</p>
    <?php endif; ?>
</div>
</body>
</html>
