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

class BiletIslemleri {
    private $baglanti;

    public function __construct($baglanti) {
        $this->baglanti = $baglanti;
    }

    public function biletleriGetir($musteri_id) {
        $stmt = $this->baglanti->query("SELECT * FROM biletsatis WHERE musteri_id = '$musteri_id'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function biletIptalEt($bilet_id) {
        $stmt = $this->baglanti->prepare("DELETE FROM biletsatis WHERE bilet_id = :bilet_id");
        $stmt->bindParam(':bilet_id', $bilet_id);
        return $stmt->execute();
    }
}

if (!isset($_SESSION["musteri_id"])) {
    header("Location: giris.php");
    exit;
}

$veritabani = new Veritabani();
$biletIslemleri = new BiletIslemleri($veritabani->baglanti());
$musteri_id = $_SESSION["musteri_id"];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bilet_id'])) {
    $bilet_id = $_POST['bilet_id'];
    if ($biletIslemleri->biletIptalEt($bilet_id)) {
        $success_message = "Bilet başarıyla iptal edildi.";
        header('Refresh:2; biletlerim.php');
        exit;
    } else {
        echo "Hata: Bilet iptal edilemedi.";
    }
}

$biletler = $biletIslemleri->biletleriGetir($musteri_id);
?>

<!DOCTYPE html>
<html lang="tr-TR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sinema Bilet Otomasyonu - Biletlerim</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f0;
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
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #333;
            color: #fff;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .btn {
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-cancel {
            background-color: #f44336;
            color: white;
        }

        .btn-cancel:hover {
            background-color: #d32f2f;
        }

        .btn-details {
            background-color: #4CAF50;
            color: white;
        }

        .btn-details:hover {
            background-color: #45a049;
        }

        .seat {
            display: inline-block;
            width: 30px;
            height: 30px;
            background-color: #ccc;
            margin: 5px;
            text-align: center;
            line-height: 30px;
        }

        .selected-seat {
            background-color: green !important;
        }

       
        #ticket-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            width: 300px;
            display: none;
        }

        #ticket-content {
            text-align: left;
        }

        #ticket-modal button {
            display: block;
            margin: 10px 0;
            padding: 10px 20px;
            border: none;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }

        #ticket-modal button:hover {
            background-color: #45a049;
        }

        #ticket-modal #close-btn {
            background-color: #f44336;
        }

        #ticket-modal #close-btn:hover {
            background-color: #d32f2f;
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
<div class="container">
    <h2>Biletlerim</h2>
    <?php if (!empty($success_message)) echo '<p style="color: green;">' . $success_message . '</p>'; ?>
    <table>
        <thead>
        <tr>
            <th>Film</th>
            <th>Salon</th>
            <th>Seans</th>
            <th>Koltuk</th>
            <th>Bilet Türü</th>
            <th>Fiyat</th>
            <th>İptal Et</th>
            <th>Detaylar</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($biletler)) {
            foreach ($biletler as $row) {
                echo "<tr>";
                echo "<td>" . $row["filmAdi"] . "</td>";
                echo "<td>" . $row["salon"] . "</td>";
                echo "<td>" . $row["seans"] . "</td>";
                echo "<td class='seat'>" . $row["koltuk"] . "</td>";
                echo "<td>" . $row["biletTürü"] . "</td>";
                echo "<td>" . $row["fiyat"] . " TL</td>";
                echo "<td><form method='post'><input type='hidden' name='bilet_id' value='" . $row["bilet_id"] . "'><button type='submit' name='iptal' class='btn btn-cancel'>İptal Et</button></form></td>";
                echo "<td><button class='btn btn-details' data-bilet='" . json_encode($row) . "'>Detaylar</button></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>Veri bulunamadı</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<div id="ticket-modal">
    <div id="ticket-content">
        <h2>Bilet Detayları</h2>
        <p><strong>Film:</strong> <span id="ticket-film"></span></p>
        <p><strong>Salon:</strong> <span id="ticket-salon"></span></p>
        <p><strong>Seans:</strong> <span id="ticket-seans"></span></p>
        <p><strong>Koltuk:</strong> <span id="ticket-seat"></span></p>
        <p><strong>Bilet Türü:</strong> <span id="ticket-type"></span></p>
        <p><strong>Fiyat:</strong> <span id="ticket-price"></span> TL</p>
        <button onclick="printTicket()">Bileti Yazdır</button>
        <button id="close-btn" onclick="closeModal()">Kapat</button>
    </div>
</div>

<script>
$(document).ready(function () {
    $('.btn-details').click(function () {
        var ticket = $(this).data('bilet');
        $('#ticket-film').text(ticket.filmAdi);
        $('#ticket-salon').text(ticket.salon);
        $('#ticket-seans').text(ticket.seans);
        $('#ticket-seat').text(ticket.koltuk);
        $('#ticket-type').text(ticket.biletTürü);
        $('#ticket-price').text(ticket.fiyat);
        $('#ticket-modal').show();
    });
});

function closeModal() {
    $('#ticket-modal').hide();
}

function printTicket() {
    var printContents = document.getElementById('ticket-content').innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload(); 
}
</script>

</body>
</html>
