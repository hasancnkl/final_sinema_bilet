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

    public function biletleriGetir($kullanici_id) {
        $biletSorgu = $this->pdo->prepare("SELECT * FROM biletsatis WHERE musteri_id = :musteri_id");
        $biletSorgu->bindParam(":musteri_id", $kullanici_id, PDO::PARAM_INT);
        $biletSorgu->execute();
        return $biletSorgu->fetchAll(PDO::FETCH_ASSOC);
    }

    public function biletSil($bilet_id) {
        $biletSilmeSorgu = $this->pdo->prepare("DELETE FROM biletsatis WHERE bilet_id = :bilet_id");
        $biletSilmeSorgu->bindParam(":bilet_id", $bilet_id, PDO::PARAM_INT);
        $biletSilmeSorgu->execute();
    }

    public function kullaniciSil($kullanici_id) {
        $kullaniciSilmeSorgu = $this->pdo->prepare("DELETE FROM kullanici_giris WHERE id = :musteri_id");
        $kullaniciSilmeSorgu->bindParam(":musteri_id", $kullanici_id, PDO::PARAM_INT);
        $kullaniciSilmeSorgu->execute();
        return $kullaniciSilmeSorgu->rowCount() > 0;
    }
}

if (isset($_GET['id'])) {
    $kullanici_id = $_GET['id'];
    $kullaniciNesne = new Kullanici($pdo);

    $biletler = $kullaniciNesne->biletleriGetir($kullanici_id);

    foreach ($biletler as $bilet) {
        $kullaniciNesne->biletSil($bilet['bilet_id']);
    }

    if ($kullaniciNesne->kullaniciSil($kullanici_id)) {
        $_SESSION['silme_mesaji'] = 'Kullanıcı ve biletleri başarıyla silindi.';
        $_SESSION['silme_durumu'] = 'success';
    } else {
        $_SESSION['silme_mesaji'] = 'Kullanıcı silme işlemi başarısız.';
        $_SESSION['silme_durumu'] = 'error';
    }

    header('Location: admin_panel.php');
    exit();
}
?>
