<?php
session_start();

class Veritabani {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "sinema";
    private $baglanti;

    
    public function baglan() {
        $this->baglanti = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);
        if (!$this->baglanti) {
            die("Bağlantı hatası: " . mysqli_connect_error());
        }
    }

    
    public function baglantiKapat() {
        mysqli_close($this->baglanti);
    }

   
    public function biletSatis($musteri_id, $filmAdi, $seans, $koltuk, $biletTuru, $fiyat, $tarih, $salon) {
        $sql = "INSERT INTO biletsatis (musteri_id, filmAdi, seans, koltuk, biletTürü, fiyat, tarih, salon)
                VALUES ('$musteri_id', '$filmAdi', '$seans', '$koltuk', '$biletTuru', '$fiyat', '$tarih', '$salon')";

        if (mysqli_query($this->baglanti, $sql)) {
            header("Location: index.php");
            exit();
        } else {
            echo "Hata: " . $sql . "<br>" . mysqli_error($this->baglanti);
        }
    }
}


$filmAdi = $_POST['filmAdi'];
$seans = $_POST['seans'];
$koltuk = $_POST['koltuk'];
$biletTuru = $_POST['biletTuru'];
$tarih = $_POST['tarih'];
$salon = $_POST['salon'];

$fiyatListe = array("ogrenci" => 50, "tam" => 70);
$fiyat = isset($fiyatListe[$biletTuru]) ? $fiyatListe[$biletTuru] : 0;

$musteri_id = $_SESSION["musteri_id"];


$veritabani = new Veritabani();
$veritabani->baglan();
$veritabani->biletSatis($musteri_id, $filmAdi, $seans, $koltuk, $biletTuru, $fiyat, $tarih, $salon);
$veritabani->baglantiKapat();
?>
