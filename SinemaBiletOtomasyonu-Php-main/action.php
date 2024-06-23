<?php
class Veritabani {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "sinema";
    private $baglanti;

    
    public function __construct() {
        $this->baglanti = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);
        if (!$this->baglanti) {
            die("Bağlantı hatası: " . mysqli_connect_error());
        }
    }

    
    public function salonGetir($filmAdi) {
        $sql = "SELECT * FROM seanslar WHERE filmAdi = ? LIMIT 1";
        $stmt = $this->baglanti->prepare($sql);
        $stmt->bind_param('s', $filmAdi);
        $stmt->execute();
        $sonuc = $stmt->get_result();
        
        $myOption = '<option value="" disabled selected>--Salon Seçiniz--</option>';
        while($row = $sonuc->fetch_assoc()) {
            $myOption .= '<option value="'.$row["salon"].'">'.$row["salon"].'</option>';
        }

        $stmt->close();
        return $myOption;
    }

    
    public function baglantiKapat() {
        mysqli_close($this->baglanti);
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filmAdi'])) {
    $veritabani = new Veritabani();
    echo $veritabani->salonGetir($_POST['filmAdi']);
    $veritabani->baglantiKapat();
}
?>
