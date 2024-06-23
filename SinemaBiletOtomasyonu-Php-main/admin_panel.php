<?php
session_start();

class AdminPaneli {
    private $veritabani;
    private $adminId;
    private $silmeMesaji;
    private $silmeDurumu;

    public function __construct() {
        $this->veritabaniBaglantisi();
        $this->oturumDegiskenleriniBaslat();
    }

    private function veritabaniBaglantisi() {
        $sunucuAdi = "localhost";
        $kullaniciAdi = "root";
        $sifre = "";
        $veritabaniAdi = "sinema";

        try {
            $this->veritabani = new PDO("mysql:host=$sunucuAdi;dbname=$veritabaniAdi", $kullaniciAdi, $sifre);
            $this->veritabani->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Veritabanı bağlantısı başarısız: " . $e->getMessage());
        }
    }

    private function oturumDegiskenleriniBaslat() {
        $this->adminId = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;
        $this->silmeMesaji = isset($_SESSION['silme_mesaji']) ? $_SESSION['silme_mesaji'] : null;
        $this->silmeDurumu = isset($_SESSION['silme_durumu']) ? $_SESSION['silme_durumu'] : null;

        unset($_SESSION['silme_mesaji']);
        unset($_SESSION['silme_durumu']);
    }

    public function girisIsle() {
        if (!$this->adminId && isset($_POST['giris'])) {
            
        }
    }

    public function getIstekIsle() {
        if (isset($_GET['id'])) {
           
        }
    }

    public function kullanicilariGetir() {
        $sorgu = $this->veritabani->query("SELECT * FROM kullanici_giris");
        return $sorgu->fetchAll(PDO::FETCH_ASSOC);
    }

    public function filmSayisiniGetir() {
        $sorgu = $this->veritabani->query("SELECT COUNT(*) as toplam FROM filmler1");
        return $sorgu->fetch(PDO::FETCH_ASSOC)['toplam'];
    }

    public function enSonKullanicilariGetir() {
        $sorgu = $this->veritabani->query("SELECT * FROM kullanici_giris ORDER BY id DESC LIMIT 5");
        return $sorgu->fetchAll(PDO::FETCH_ASSOC);
    }

    public function mesajlariGetir() {
        $sorgu = $this->veritabani->query("SELECT * FROM iletisim_mesaj");
        return $sorgu->fetchAll(PDO::FETCH_ASSOC);
    }

    public function silmeMesajiGetir() {
        return $this->silmeMesaji;
    }

    public function silmeDurumuGetir() {
        return $this->silmeDurumu;
    }
}


$adminPaneli = new AdminPaneli();
$adminPaneli->girisIsle();
$adminPaneli->getIstekIsle();
$kullanicilar = $adminPaneli->kullanicilariGetir();
$filmSayisi = $adminPaneli->filmSayisiniGetir();
$enSonKullanicilar = $adminPaneli->enSonKullanicilariGetir();
$mesajlar = $adminPaneli->mesajlariGetir();
$silmeMesaji = $adminPaneli->silmeMesajiGetir();
$silmeDurumu = $adminPaneli->silmeDurumuGetir();

$hakkimizdaDuzenle = "hakkimizda_duzenle.php";
$filmEkle = "FilmEkle.php";

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <style>
      
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Roboto', Arial, sans-serif;
    background-color: #f8f9fa;
    color: #333;
    line-height: 1.6;
}

header, footer {
    background-color: #343a40;
    color: #fff;
    padding: 20px;
    text-align: center;
}

nav {
    background-color: #495057;
    padding: 10px 0;
    text-align: center;
}

nav a {
    color: #fff;
    text-decoration: none;
    padding: 10px 20px;
    margin: 0 5px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

nav a:hover {
    background-color: #6c757d;
}

main {
    padding: 20px;
    max-width: 1200px;
    margin: 20px auto;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

.error-message {
    background-color: #dc3545;
    color: #fff;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
}

.dashboard-stats {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 30px;
}

.istatistik-kutusu {
    flex: 1;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    min-width: 250px;
    text-align: center;
}

.istatistik-kutusu h3 {
    margin-bottom: 10px;
    color: #666;
    font-size: 1.2rem;
}

.istatistik-kutusu p {
    font-size: 1.8rem;
    font-weight: bold;
}

.kullanici-listesi, .mesaj-listesi {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

th, td {
    padding: 12px 15px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

th {
    background-color: #f4f4f4;
    font-weight: 600;
}

a.dugme-ekle, a.dugme-duzenle {
    display: inline-block;
    background-color: #007bff;
    color: #fff;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

a.dugme-ekle:hover, a.dugme-duzenle:hover {
    background-color: #0056b3;
}

footer {
    background-color: #343a40;
    color: #fff;
    text-align: center;
    padding: 10px 0;
    position: fixed;
    bottom: 0;
    width: 100%;
    box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
}


    </style>
</head>
<body>
    <header>
        <h1>Sinema-Bilet Otomasyonu - Admin Paneli</h1>
        <p>Hoş geldiniz, <?php echo isset($_SESSION['kullanici_adi']) ? $_SESSION['kullanici_adi'] : ''; ?>!</p>
    </header>

    <nav>
        <a href="#dashboard">Panel</a>
        <a href="#kullanicilar">Kullanıcılar</a>
        <a href="#mesajlar">Mesajlar</a>
        <a href="#filmekle">Film Ekle</a>
        <a href="#hakkimizda">Hakkımızda</a>
        <a href="admin_giris.php">Çıkış Yap</a>
    </nav>

    <main>
        <?php if (isset($hata_mesaji)) : ?>
            <div class="error-message"><?php echo $hata_mesaji; ?></div>
        <?php endif; ?>

        <section id="dashboard">
            <h2>Genel Bakış</h2>

            <div class="dashboard-stats">
                <div class="istatistik-kutusu">
                    <h3>Toplam Kullanıcı Sayısı</h3>
                    <p><?php echo count($kullanicilar); ?></p>
                </div>

                <div class="istatistik-kutusu">
                    <h3>Toplam Vizyondaki Film Sayısı</h3>
                    <p><?php echo $filmSayisi; ?></p>
                </div>

                <div class="istatistik-kutusu">
                    <h3>En Son Eklenen Kullanıcılar</h3>
                    <ul class="recent-users-list">
                        <?php foreach ($enSonKullanicilar as $sonKullanici) : ?>
                            <li><?php echo $sonKullanici['adsoyad']; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </section>

        <section id="kullanicilar">
            <h2>Kullanıcılar</h2>
            <div class="kullanici-listesi">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kullanıcı Adı</th>
                            <th>Adı Soyadı</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kullanicilar as $kullanici) : ?>
                            <tr>
                                <td><?php echo $kullanici['id']; ?></td>
                                <td><?php echo $kullanici['mail']; ?></td>
                                <td><?php echo $kullanici['adsoyad']; ?></td>
                                <td>
                                    <a href="panel_detay.php?id=<?php echo $kullanici['id']; ?>" class="dugme-duzenle" style="background-color: #6c757d;">Detay</a>
                                    <a href="panel_duzenle.php?id=<?php echo $kullanici['id']; ?>" class="dugme-duzenle" style="background-color: #ffc107;">Düzenle</a>
                                    <a href="panel_kullanici_sil.php?id=<?php echo $kullanici['id']; ?>" onclick="return confirm('Silmek istediğinizden emin misiniz?')" class="dugme-duzenle" style="background-color: #e74c3c;">Sil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <a class="dugme-ekle" href="kullanici_ekle.php">Yeni Kullanıcı Ekle</a>
            </div>
        </section>

        <section id="mesajlar">
            <h2>Mesajlar</h2>
            <div class="mesaj-listesi">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>İsim</th>
                            <th>Email</th>
                            <th>Konu</th>
                            <th>Mesaj</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mesajlar as $mesaj) : ?>
                            <tr>
                                <td><?php echo $mesaj['id']; ?></td>
                                <td><?php echo $mesaj['isim']; ?></td>
                                <td><?php echo $mesaj['email']; ?></td>
                                <td><?php echo $mesaj['konu']; ?></td>
                                <td><?php echo $mesaj['mesaj']; ?></td>
                                <td>
                                    <a href="panel_mesaj_sil.php?id=<?php echo $mesaj['id']; ?>" onclick="return confirm('Bu mesajı silmek istediğinizden emin misiniz?')" class="dugme-duzenle" style="background-color: #e74c3c;">Sil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="hakkimizda">
            <h2>Hakkımızda</h2>
            <p>Hakkımızda kısmını düzenlemek için aşağıdaki butona basabilirsiniz.</p>
            <a class="dugme-duzenle" href="<?php echo $hakkimizdaDuzenle; ?>" style="background-color: #6c757d;">Hakkımızda Düzenle</a>
        </section>
<br>
<br>
        <section id="filmekle">
            <h2>Filmler</h2>
            <p>Film eklemek ve düzenlemek için aşağıdaki butona basınız.</p>
            <a class="dugme-duzenle" href="<?php echo $filmEkle; ?>">Film Ekle</a>
            <a class="dugme-duzenle" href="salonekle.php">Salon Ekle</a>
            <a class="dugme-duzenle" href="seansEkle.php">Seans Ekle</a>
            <a class="dugme-duzenle" href="hasılat.php">Hasılat</a>
        </section>
    </main>
<br>
<br>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Admin Paneli</p>
    </footer>
</body>
</html>
