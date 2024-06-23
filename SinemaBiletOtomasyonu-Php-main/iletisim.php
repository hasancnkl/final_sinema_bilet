<?php
class IletisimFormu {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "sinema";
    private $conn;

    public function __construct() {
        $this->veritabaniBaglantisi();
    }

    private function veritabaniBaglantisi() {
        try {
            $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Bağlantı hatası: " . $e->getMessage());
        }
    }

    public function mesajGonder($isim, $email, $konu, $mesaj) {
        if (!$isim || !$email || !$konu || !$mesaj) {
            return '<div class="alert alert-danger">Boş Alan Bırakmayınız</div>';
        }

        try {
            $stmt = $this->conn->prepare('INSERT INTO iletisim_mesaj (isim, email, konu, mesaj) VALUES (?, ?, ?, ?)');
            $stmt->execute([$isim, $email, $konu, $mesaj]);

            if ($stmt->rowCount() > 0) {
                header('Refresh:2; anasayfa.php');
                return '<div class="alert alert-success">Mesaj gönderme Başarılı</div>';
            } else {
                return '<div class="alert alert-danger">Mesaj gönderme Başarısız</div>';
            }
        } catch (PDOException $e) {
            return 'Hata: ' . $e->getMessage();
        }
    }
}

$iletisimFormu = new IletisimFormu();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Gonder'])) {
    $isim = $_POST['isim'];
    $email = $_POST['email'];
    $konu = $_POST['konu'];
    $mesaj = $_POST['mesaj'];

    $success_message = $iletisimFormu->mesajGonder($isim, $email, $konu, $mesaj);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İletişim</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
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
        .contact-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .contact-container h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
            color: #343a40;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #343a40;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            transition: border-color 0.3s ease-in-out;
        }
        .form-control:focus {
            border-color: #007bff;
        }
        .btn-primary {
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            border: none;
            background-color: #007bff;
            color: #fff;
            transition: background-color 0.3s ease-in-out;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            border: none;
            background-color: #dc3545;
            color: #fff;
            margin-top: 10px;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .alert {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        #editor {
            border: 1px solid #ced4da;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<nav>
    <a href="index.php">Anasayfa</a>
    <a href="filmler.php">Vizyondakiler</a>
    <a href="biletlerim.php">Biletlerim</a>
    <a href="iletisim.php">İletişim</a>
    <a href="hakkimizda.php">Hakkımızda</a>
    <a href="giris.php">Çıkış Yap</a>
</nav>
<div class="contact-container">
    <h2>İletişim</h2>
    <?php if (isset($success_message)) echo $success_message; ?>
    <form action="iletisim.php" method="POST">
        <div class="form-group">
            <label for="isim">İsim:</label>
            <input type="text" id="isim" name="isim" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="konu">Konu:</label>
            <input type="text" id="konu" name="konu" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="mesaj">Mesajınız:</label>
            <textarea id="mesaj" name="mesaj" class="form-control" style="display: none;"></textarea>
            <div id="editor"></div>
        </div>
        <button type="submit" name="Gonder" class="btn btn-primary">Gönder</button>
    </form>
    <form action="mesaj_sil.php" method="POST">
        <button type="submit" name="sil" class="btn btn-danger">MESAJLARIMI SİL</button>
    </form>
</div>
<script>
    ClassicEditor
        .create(document.querySelector('#editor'))
        .then(editor => {
            editor.model.document.on('change', () => {
                document.getElementById('mesaj').value = editor.getData();
            });
        })
        .catch(error => {
            console.error(error);
        });
</script>
</body>
</html>
