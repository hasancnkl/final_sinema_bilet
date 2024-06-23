<?php
session_start();

class SinemaBiletOtomasyonu {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "sinema";
    private $conn;
    public $musteri_id;

    public function __construct() {
        if (!isset($_SESSION["musteri_id"])) {
            header("Location: giris.php");
            exit;
        }
        $this->musteri_id = $_SESSION["musteri_id"];
        $this->connectDatabase();
    }

    private function connectDatabase() {
        try {
            $this->conn = new PDO("mysql:host={$this->servername};dbname={$this->dbname}", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Hata: " . $e->getMessage();
            exit;
        }
    }

    public function getDoluKoltuklar($tarih, $filmAdi, $seans, $salon) {
        $doluKoltuklar = array();
        $sql = "SELECT koltuk FROM biletsatis WHERE tarih = :tarih AND filmAdi = :filmAdi AND seans = :seans AND salon = :salon";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tarih', $tarih);
        $stmt->bindParam(':filmAdi', $filmAdi);
        $stmt->bindParam(':seans', $seans);
        $stmt->bindParam(':salon', $salon);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $doluKoltuklar[] = $row['koltuk'];
        }
        return $doluKoltuklar;
    }

    public function biletSatis($tarih, $filmAdi, $seans, $salon, $koltuk, $biletTuru) {
        $sql = "INSERT INTO biletsatis (tarih, filmAdi, seans, salon, koltuk, biletTuru, musteri_id) 
                VALUES (:tarih, :filmAdi, :seans, :salon, :koltuk, :biletTuru, :musteri_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tarih', $tarih);
        $stmt->bindParam(':filmAdi', $filmAdi);
        $stmt->bindParam(':seans', $seans);
        $stmt->bindParam(':salon', $salon);
        $stmt->bindParam(':koltuk', $koltuk);
        $stmt->bindParam(':biletTuru', $biletTuru);
        $stmt->bindParam(':musteri_id', $this->musteri_id);

        return $stmt->execute();
    }
}

$sinema = new SinemaBiletOtomasyonu();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tarih = $_POST['tarih'];
    $filmAdi = $_POST['filmAdi'];
    $seans = $_POST['seans'];
    $salon = $_POST['salon'];
    $koltuk = isset($_POST['koltuk']) ? $_POST['koltuk'] : '';
    $biletTuru = isset($_POST['biletTuru']) ? $_POST['biletTuru'] : '';

    if (!$tarih || !$filmAdi || !$seans || !$salon) {
        echo '<div class="alert alert-danger text-center" role="alert"><strong>Boş Alan Bırakmayınız</strong></div>';
        header('Refresh:2; index.php');
    } else {
        if ($sinema->biletSatis($tarih, $filmAdi, $seans, $salon, $koltuk, $biletTuru)) {
            echo '<div class="alert alert-success text-center" role="alert"><strong>Kayıt Başarılı</strong></div>';
            header('Refresh:2; index.php');
        } else {
            echo '<div class="alert alert-danger text-center" role="alert"><strong>Kayıt Başarısız</strong></div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sinema Bilet Otomasyonu</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#filmAdi').change(function() {
                var filmAdi = $(this).val();
                $.ajax({
                    url: "ajax.php",
                    method: "POST",
                    data: {filmAdi: filmAdi},
                    dataType: "text",
                    success: function(data) {
                        $('#seans').html(data);
                    }
                });
                $.ajax({
                    url: "filmBilgileri.php",
                    method: "POST",
                    data: {filmAdi: filmAdi},
                    dataType: "text",
                    success: function(data) {
                        $('.filmBilgileri').html(data);
                    }
                });
            });

            $('#seans').change(function() {
                var filmAdi = $('#filmAdi').val();
                $.ajax({
                    url: "action.php",
                    method: "POST",
                    data: {filmAdi: filmAdi},
                    dataType: "text",
                    success: function(data) {
                        $('#salon').html(data);
                    }
                });
            });
        });

        window.onload = function() {
            const seat = document.querySelector('.konteyner');
            if (seat != null) {
                seat.addEventListener('click', function(e) {
                    if (e.target.classList.contains('koltuk') && !e.target.classList.contains('rezerve')) {
                        e.target.classList.toggle('secili');
                        let koltuk = document.getElementById("koltuk");
                        let selectedSeats = document.querySelectorAll('.koltuk.secili');
                        let selectedSeatNumbers = Array.from(selectedSeats).map(seat => seat.innerText);
                        koltuk.value = selectedSeatNumbers.join(', ');
                    }
                });
            } else {
                console.log("Eleman Bulunamadı");
            }
        };

        function fiyatHesapla() {
            let biletTuru = document.getElementById("biletTuru").value;
            let fiyat = document.getElementById("fiyat");
            let selectedSeats = document.querySelectorAll('.koltuk.secili');

            if (biletTuru === "ogrenci") {
                fiyat.innerHTML = selectedSeats.length * 50;
            } else if (biletTuru === "tam") {
                fiyat.innerHTML = selectedSeats.length * 70;
            } else {
                fiyat.innerHTML = "";
            }
        }

        function formDogrula() {
            var tarih = document.getElementById("tarih").value;
            var filmAdi = document.getElementById("filmAdi").value;
            var seans = document.getElementById("seans").value;
            var salon = document.getElementById("salon").value;
            var koltuk = document.getElementById("koltuk").value;
            var biletTuru = document.getElementById("biletTuru").value;

            if (tarih == "" || filmAdi == "" || seans == "" || salon == "" || koltuk == "" || biletTuru == "") {
                alert("Lütfen tüm bilgileri doldurun.");
                return false;
            }

            alert("Bilet başarıyla oluşturuldu!");
            return true;
        }
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
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

        form {
            background-color: #fff;
            padding: 20px;
            margin: 20px auto;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-grup {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
        }

        input[type="date"],
        select,
        input[type="text"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-top: 5px;
        }

        select {
            cursor: pointer;
        }

        button[type="submit"] {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        button[type="submit"]:hover {
            background-color: #555;
        }

        .filmBilgileri {
            background-color: #fff;
            padding: 20px;
            margin: 20px auto;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .konteyner {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 20px auto;
            max-width: 600px;
        }

        .satir {
            display: flex;
            justify-content: center;
            margin-bottom: 10px;
        }

        .koltuk {
            width: 40px;
            height: 40px;
            background-color: #ccc;
            border-radius: 5px;
            margin: 5px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }

        .koltuk.rezerve {
            background-color: #ff0000;
            cursor: not-allowed;
        }

        .koltuk.secili {
            background-color: #3c8dbc;
            color: #fff;
        }

        .perde {
            width: 100%;
            height: 30px;
            background-color: #333;
            text-align: center;
            color: #fff;
            margin-bottom: 20px;
            border-radius: 5px;
            line-height: 30px;
        }
        

        @media(max-width: 768px) {
            .konteyner {
                max-width: 90%;
            }
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
    <form action="biletSatis.php" method="post" onsubmit="return formDogrula()">
        <h2 style="text-align:center;">Bilet Satış</h2>
        <div class="form-grup">
            <label for="tarih">Tarih: </label>
            <input type="date" name="tarih" id="tarih" value="<?php echo date('Y-m-d'); ?>">
        </div>
        <div class="form-grup">
            <label for="filmAdi">Film: </label>
            <select name="filmAdi" id="filmAdi">
                <option value="" disabled selected>--Film Seçiniz--</option>
                <?php
                $conn = mysqli_connect("localhost", "root", "", "sinema");
                $result = mysqli_query($conn, "SELECT * FROM filmler1");
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row["filmAdi"] . "'>" . $row["filmAdi"] . "</option>";
                }
                mysqli_close($conn);
                ?>
            </select>
        </div>
        <div class="form-grup">
            <label for="seans">Seans: </label>
            <select name="seans" id="seans">
                <option value="">--Seans Seçiniz--</option>
            </select>
        </div>
        <div class="form-grup">
            <label for="salon">Salon: </label>
            <select name="salon" id="salon">
                <option value="">--Salon Seçiniz--</option>
            </select>
        </div>
        <div class="form-grup">
            <label for="koltuk">Koltuk: </label>
            <input type="text" name="koltuk" id="koltuk" readonly>
        </div>
        <div class="form-grup">
            <label for="biletTuru">Bilet Türü: </label>
            <select name="biletTuru" id="biletTuru" onchange="fiyatHesapla()">
                <option value="" disabled selected>--Bilet Türünü Seçiniz--</option>
                <option value="tam">Tam</option>
                <option value="ogrenci">Öğrenci</option>
            </select>
        </div>
        <div class="form-grup">
            <p>Fiyat: <span id="fiyat"></span> TL</p>
        </div>
        <div class="form-grup">
            <button type="submit">Bilet Oluştur</button>
        </div>
    </form>
    <div class="filmBilgileri">
        <p>Film Adı: <span id="filmAdiSpan"></span></p>
        <p>Yönetmen: <span id="yonetmenSpan"></span></p>
        <p>Tür: <span id="turSpan"></span></p>
        <p>Süre: <span id="sureSpan"></span></p>
        <p>Açıklama:</p>
        <textarea name="aciklama" id="aciklama" rows="15" cols="30" readonly></textarea>
    </div>
    <div class="konteyner">
        <div class="perde">Perde</div>
        <?php
        
        $alfabe = range('A', 'E');
        $doluKoltuklar = $sinema->getDoluKoltuklar(date('Y-m-d'), 'Film Adı', 'Seans Saati', 'Salon Adı');
        foreach ($alfabe as $harf) {
            echo "<div class='satir'>";
            for ($i = 1; $i <= 8; $i++) {
                $koltuk = $harf . $i;
                $sinif = in_array($koltuk, $doluKoltuklar) ? 'koltuk rezerve' : 'koltuk';
                echo "<div class='$sinif'>$koltuk</div>";
            }
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>
