<?php
class Database {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "sinema";
    public $conn;

    public function __construct() {
        $this->conn = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);
        if (!$this->conn) {
            die("Bağlantı hatası: " . mysqli_connect_error());
        }
    }

    public function query($sql) {
        return mysqli_query($this->conn, $sql);
    }

    public function close() {
        mysqli_close($this->conn);
    }
}

class Seans {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function ekle($filmAdi, $salon, $seans) {
        $filmAdi = htmlspecialchars($filmAdi);
        $salon = htmlspecialchars($salon);
        $seans = htmlspecialchars($seans);

        if(empty($filmAdi) || empty($salon) || empty($seans)) {
            die("Hata: Form verileri eksik.");
        }

        $sql = "INSERT INTO seanslar (filmAdi, salon, seans) VALUES ('$filmAdi', '$salon', '$seans')";
        if ($this->db->query($sql)) {
            header("Location: seansEkle.php?status=success");
            exit();
        } else {
            echo "Hata: " . $sql . "<br>" . mysqli_error($this->db->conn);
        }
    }

    public function getFilmler() {
        $sql = "SELECT filmAdi FROM filmler1";
        return $this->db->query($sql);
    }

    public function getSalonlar() {
        $sql = "SELECT salonAdi FROM salonlar";
        return $this->db->query($sql);
    }

    public function __destruct() {
        $this->db->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $seans = new Seans();
    $seans->ekle($_POST['filmAdi'], $_POST['salon'], $_POST['seans']);
}
?>
<!DOCTYPE html>
<html lang="tr-TR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sinema Bilet Otomasyonu</title>
    <style>
        
        body {
            font-family: Arial, sans-serif;
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
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .seansEkle {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        select, input[type="text"], input[type="date"], input[type="radio"] {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 5px;
        }

        input[type="radio"] {
            width: auto;
            margin: 5px;
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
        }

        button:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <nav>
        <a href="admin_panel.php"><b>panel</b></a>
    </nav>
    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert alert-success text-center" role="alert">
            <strong>Salon ekleme işlemi başarılı!</strong>
        </div>
        <?php header('Refresh:2; url=admin_panel.php'); ?>
    <?php endif; ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="seansEkle">
            <label for="filmAdi">Film:</label>
            <select name="filmAdi" id="filmAdi">
                <option value="">--Film Seçiniz--</option>
                <?php
                $seans = new Seans();
                $filmler = $seans->getFilmler();
                if (mysqli_num_rows($filmler) > 0) {
                    while($row = mysqli_fetch_assoc($filmler)) {
                        echo "<option value='" . $row['filmAdi'] . "'>" . $row['filmAdi'] . "</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div class="seansEkle">
            <label for="salon">Salon:</label>
            <select name="salon" id="salon">
                <option value="">--Salon Seçiniz--</option>
                <?php
                $salonlar = $seans->getSalonlar();
                if (mysqli_num_rows($salonlar) > 0) {
                    while($row = mysqli_fetch_assoc($salonlar)) {
                        echo "<option value='" . $row['salonAdi'] . "'>" . $row['salonAdi'] . "</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div class="seansEkle">
            <label>Seans:</label><br>
            <input type="radio" name="seans" value="11:00">11:00
            <input type="radio" name="seans" value="14:30">14:30
            <input type="radio" name="seans" value="18:00">18:00
            <input type="radio" name="seans" value="21:30">21:30
        </div>
        <div class="seansEkle">
            <button type="submit">Ekle</button>
        </div>
    </form>
</body>
</html>
