<?php
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

    
    public function filmBilgileriniGetir($filmAdi) {
        $query = "SELECT * FROM filmler1 WHERE filmAdi = ?";
        $stmt = mysqli_prepare($this->baglanti, $query);
        mysqli_stmt_bind_param($stmt, "s", $filmAdi);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $output = 'Film Adı:  <span id="baslikSpan"></span><br>
                   Puan:  <span id="puanSpan"></span><br>
                   Tür:  <span id="türSpan"></span><br>
                   Süre:  <span id="süreSpan"></span><br>
                   Açıklama:<br>  <textarea name="Açıklama" id="Açıklama" rows="15" cols="30"></textarea>';

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $output = 'Film Adı:  <span id="filmAdiSpan" style="color:red;">'.$row["filmAdi"].'</span><br>
                           Puan:  <span id="puanSpan" style="color:red;">'.$row["puan"].'</span><br>
                           Tür:  <span id="türSpan" style="color:red;">'.$row["turler"].'</span><br>
                           Süre:  <span id="süreSpan" style="color:red;">'.$row["sure"].'</span><br>
                           Açıklama:<br>  <textarea name="Açıklama" id="Açıklama" rows="15" cols="30">'.$row["aciklama"].'</textarea>';
            }
        }

        
        mysqli_stmt_close($stmt);
        return $output;
    }
}


$veritabani = new Veritabani();
$veritabani->baglan();


$selected_film = $_POST['filmAdi'];


echo $veritabani->filmBilgileriniGetir($selected_film);


$veritabani->baglantiKapat();
?>
