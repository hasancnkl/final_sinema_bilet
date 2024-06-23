<?php
session_start();

class Database {
    private $pdo;

    public function __construct($servername, $username, $password, $dbname) {
        try {
            $this->pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Veritabanı bağlantısı başarısız: " . $e->getMessage());
        }
    }

    public function getPDO() {
        return $this->pdo;
    }
}

class Kullanici {
    private $pdo;
    private $id;

    public function __construct($pdo, $id) {
        $this->pdo = $pdo;
        $this->id = $id;
    }

    public function getDetails() {
        $sorgu = $this->pdo->prepare("SELECT * FROM kullanici_giris WHERE id = :id");
        $sorgu->bindParam(':id', $this->id);
        $sorgu->execute();
        return $sorgu->fetch(PDO::FETCH_ASSOC);
    }

    public function getBiletler() {
        $sorgu = $this->pdo->prepare("SELECT filmAdi FROM biletsatis WHERE musteri_id = :id");
        $sorgu->bindParam(':id', $this->id);
        $sorgu->execute();
        return $sorgu->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBiletDetaylari($filmAdi) {
        $sorgu = $this->pdo->prepare("SELECT * FROM biletsatis WHERE musteri_id = :id AND filmAdi = :filmAdi");
        $sorgu->bindParam(':id', $this->id);
        $sorgu->bindParam(':filmAdi', $filmAdi);
        $sorgu->execute();
        return $sorgu->fetch(PDO::FETCH_ASSOC);
    }
}

$database = new Database("localhost", "root", "", "sinema");
$pdo = $database->getPDO();

$kullanici_id = isset($_GET['id']) ? $_GET['id'] : null;
$secilen_bilet = isset($_GET['bilet']) ? $_GET['bilet'] : null;

if (!$kullanici_id) {
    die("Geçersiz kullanıcı ID");
}

$kullanici = new Kullanici($pdo, $kullanici_id);
$kullanici_detaylari = $kullanici->getDetails();
$bilet_sorgu = $kullanici->getBiletler();
$bilet = $kullanici->getBiletDetaylari($secilen_bilet);

$bilet_no = $bilet['filmAdi'] ?? 'Bilgi bulunamadı';
$seans = $bilet['seans'] ?? 'Bilgi bulunamadı';
$koltuk = $bilet['koltuk'] ?? null;
$biletTürü = $bilet['biletTürü'] ?? 'Bilgi bulunamadı';
$fiyat = $bilet['fiyat'] ?? 'Bilgi bulunamadı';
$tarih = $bilet['tarih'] ?? 'Bilgi bulunamadı';
$salon = $bilet['salon'] ?? 'Bilgi bulunamadı';
$odendi = isset($bilet['odendi']) ? ($bilet['odendi'] ? 'Ödendi' : 'Ödenmedi') : 'Bilgi bulunamadı';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Detayları</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        header {
            background-color: #3498db;
            color: #fff;
            padding: 15px 0;
            text-align: center;
        }
        h1 {
            margin: 0;
            font-size: 28px;
        }
        main {
            display: flex;
            justify-content: space-between;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 20px;
            padding: 20px;
        }
        .bilet-list table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .bilet-list th, .bilet-list td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
            background-color: #f2f2f2;
        }
        .bilet-list th {
            background-color: #3498db;
            color: #fff;
        }
        .bilet-list td a {
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }
        .bilet-list td a:hover {
            text-decoration: underline;
        }
        .bilet-details p {
            margin-bottom: 16px;
        }
        .user-details, .bilet-details {
            margin-top: 20px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }
        .user-details label, .bilet-details label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .user-details p, .bilet-details p {
            margin-bottom: 16px;
        }
        .user-details a {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <h1>Kullanıcı Detayları</h1>
    </header>
    <main>
        <div class="bilet-list">
            <table>
                <tr>
                    <th>FİLMLER</th>
                </tr>
                <?php foreach ($bilet_sorgu as $bilet): ?>
                    <tr>
                        <td><a href="?id=<?= $kullanici_id ?>&bilet=<?= $bilet['filmAdi'] ?>"><?= $bilet['filmAdi'] ?></a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="bilet-details">
            <div class="user-details">
                <label for="id">ID:</label>
                <p><?= $kullanici_detaylari['id'] ?? 'Bilgi bulunamadı' ?></p>
                <label for="mail">Mail:</label>
                <p><?= $kullanici_detaylari['mail'] ?? 'Bilgi bulunamadı' ?></p>
                <label for="adsoyad">Kullanıcı Adı:</label>
                <p><?= $kullanici_detaylari['adsoyad'] ?? 'Bilgi bulunamadı' ?></p>
            </div>
            <div class="bilet-details">
                <label for="bilet_no">Film Adı:</label>
                <p><?= $bilet_no ?></p>
                <label for="seans">Seans:</label>
                <p><?= $seans ?></p>
                <label for="koltuk">Koltuk:</label>
                <p><?= $koltuk ?></p>
                <label for="biletTürü">Bilet Türü:</label>
                <p><?= $biletTürü ?></p>
                <label for="fiyat">Fiyat:</label>
                <p><?= $fiyat ?></p>
                <label for="tarih">Tarih:</label>
                <p><?= $tarih ?></p>
                <label for="salon">Salon:</label>
                <p><?= $salon ?></p>
                <label for="odendi">Ödeme Durumu:</label>
                <p><?= $odendi ?></p>
                <a href="admin_panel.php">Geri Dön</a>
            </div>
        </div>
    </main>
</body>
</html>
