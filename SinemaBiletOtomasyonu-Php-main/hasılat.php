<?php
class Veritabani {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "sinema";
    private $conn;

    public function __construct() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        if ($this->conn->connect_error) {
            die("Bağlantı hatası: " . $this->conn->connect_error);
        }
    }

    public function __destruct() {
        $this->conn->close();
    }

    public function filmAdiGetir() {
        $filmler = [];
        $result = $this->conn->query("SELECT DISTINCT filmAdi FROM biletsatis");
        while ($row = $result->fetch_assoc()) {
            $filmler[] = $row["filmAdi"];
        }
        return $filmler;
    }

    public function seansGetir($filmAdi) {
        $seanslar = [];
        $query = "SELECT DISTINCT seans FROM biletsatis WHERE filmAdi = '$filmAdi'";
        $result = $this->conn->query($query);
        while ($row = $result->fetch_assoc()) {
            $seanslar[] = $row["seans"];
        }
        return $seanslar;
    }

    public function hasilatHesapla($filmAdi, $seans, $tarih, $genelHasilat) {
        if ($genelHasilat) {
            $query = "SELECT SUM(fiyat) as hasilat FROM biletsatis WHERE filmAdi = '$filmAdi'";
        } else {
            $query = "SELECT SUM(fiyat) as hasilat FROM biletsatis WHERE filmAdi = '$filmAdi' AND seans = '$seans' AND tarih='$tarih'";
        }
        $result = $this->conn->query($query);
        $hasilat = 0;
        if ($row = $result->fetch_assoc()) {
            $hasilat = $row['hasilat'];
        }
        return $hasilat;
    }
}

if(isset($_POST['action']) && $_POST['action'] == 'getSeans' && isset($_POST['filmAdi'])) {
    $filmAdi = $_POST['filmAdi'];
    $veritabani = new Veritabani();
    $seanslar = $veritabani->seansGetir($filmAdi);
    $output = '<option value="">--Seans Seçiniz--</option>';
    foreach ($seanslar as $seans) {
        $output .= '<option value="' . $seans . '">' . $seans . '</option>';
    }
    echo $output;
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr-TR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sinema Bilet Otomasyonu</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#filmAdi').change(function(){
                var filmAdi = $(this).val();
                $.ajax({
                    url: "hasılat.php",
                    method: "POST",
                    data: {action: 'getSeans', filmAdi: filmAdi},
                    success: function(data){
                        $('#seans').html(data);
                    }
                });
            });
        });
    </script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        nav {
            background-color: #333;
            padding: 10px 0;
            text-align: center;
            margin-bottom: 20px;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            padding: 0 10px;
        }

        form {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-grup {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        select, input[type="text"], input[type="date"], input[type="checkbox"] {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 5px;
        }

        input[type="checkbox"] {
            width: auto;
            margin-top: 0;
            margin-right: 5px;
        }

        button {
            width: calc(100% - 20px);
            padding: 10px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #555;
        }

        .hesap {
            font-size: 18px;
            text-align: center;
            margin-top: 20px;
        }

        #hasilatSpan {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav>
        <a href="admin_panel.php"><b>PANEL</b></a>
    </nav>
    <form action="hasılat.php" method="post">
        <div class="form-grup">
            <label for="filmAdi">Film:</label>
            <select name="filmAdi" id="filmAdi">
                <option value="">--Film Seçiniz--</option>
                <?php
                $veritabani = new Veritabani();
                $filmler = $veritabani->filmAdiGetir();
                foreach ($filmler as $film) {
                    echo "<option value='" . $film . "'>" . $film . "</option>"; 
                }
                ?>
            </select>
        </div>
        <div class="form-grup">
            <label for="seans">Seans:</label>
            <select name="seans" id="seans">
                <option value="">--Seans Seçiniz--</option>
            </select>
        </div>
        <div class="form-grup">
            <label for="tarih">Tarih:</label>
            <input type="date" name="tarih" id="tarih">
            <input type="checkbox" name="genelhasilat" id="genelhasilat">Genel Hasılat
        </div> 
        <div class="form-grup">
            <button type="submit">Hesapla</button>
        </div> 
    </form>
    <p class="hesap">
        Hasılat: <span id="hasilatSpan">
            <?php
            if (isset($_POST['filmAdi']) && isset($_POST['seans']) && isset($_POST['tarih'])){
                $filmAdi = $_POST['filmAdi'];
                $seans = $_POST['seans'];
                $tarih = $_POST['tarih'];
                $genelHasilat = isset($_POST['genelhasilat']) && $_POST['genelhasilat'] == 'on';

                $veritabani = new Veritabani();
                $hasilat = $veritabani->hasilatHesapla($filmAdi, $seans, $tarih, $genelHasilat);
                echo $hasilat;
            }
            else {
                echo "0";
            }
            ?>
        </span> TL
    </p>
</body>
</html>

