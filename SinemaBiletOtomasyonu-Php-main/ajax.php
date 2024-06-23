<?php

class SinemaDatabase {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "sinema";
    private $conn;

    public function __construct() {
        $this->conn = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);

        if (mysqli_connect_errno()) {
            die("Veritabanı bağlantısı başarısız: " . mysqli_connect_error());
        }
    }

    public function getSeanslar($filmAdi) {
        $output = '';
        $query = "SELECT * FROM seanslar WHERE filmAdi = ?";
        $stmt = mysqli_prepare($this->conn, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $filmAdi);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $output .= '<option value="" disabled selected>--Seans Seçiniz--</option>';
            while ($row = mysqli_fetch_array($result)) {
                $output .= '<option value="' . $row["seans"] . '">' . $row["seans"] . '</option>';
            }

            mysqli_stmt_close($stmt);
        } else {
            die("Sorgu hazırlanamadı: " . mysqli_error($this->conn));
        }

        return $output;
    }

    public function __destruct() {
        mysqli_close($this->conn);
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['filmAdi'])) {
    $sinemaDB = new SinemaDatabase();
    echo $sinemaDB->getSeanslar($_POST['filmAdi']);
}

?>
