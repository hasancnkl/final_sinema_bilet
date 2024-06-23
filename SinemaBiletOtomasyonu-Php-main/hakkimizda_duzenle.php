<?php
session_start();

class Hakkimizda {
    private $pdo;

    public function __construct($servername, $username, $password, $dbname) {
        try {
            $this->pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Veritabanı bağlantısı başarısız: " . $e->getMessage());
        }
    }

    public function getBilgiler() {
        $bilgiler_sorgu = $this->pdo->query("SELECT * FROM hakkimizda WHERE id = 1");
        return $bilgiler_sorgu->fetch(PDO::FETCH_ASSOC);
    }

    public function guncelle($data) {
        $guncelle_sorgu = $this->pdo->prepare("UPDATE hakkimizda SET 
            firma_adi = :firma_adi, 
            hosgeldiniz_baslik = :hosgeldiniz_baslik, 
            vizyon_baslik = :vizyon_baslik, 
            vizyon_aciklama = :vizyon_aciklama, 
            misyon_baslik = :misyon_baslik, 
            misyon_aciklama = :misyon_aciklama, 
            iletisim_eposta = :iletisim_eposta, 
            iletisim_telefon = :iletisim_telefon, 
            iletisim_adres = :iletisim_adres 
            WHERE id = 1");

        $guncelle_sorgu->bindParam(':firma_adi', $data['firma_adi'], PDO::PARAM_STR);
        $guncelle_sorgu->bindParam(':hosgeldiniz_baslik', $data['hosgeldiniz_baslik'], PDO::PARAM_STR);
        $guncelle_sorgu->bindParam(':vizyon_baslik', $data['vizyon_baslik'], PDO::PARAM_STR);
        $guncelle_sorgu->bindParam(':vizyon_aciklama', $data['vizyon_aciklama'], PDO::PARAM_STR);
        $guncelle_sorgu->bindParam(':misyon_baslik', $data['misyon_baslik'], PDO::PARAM_STR);
        $guncelle_sorgu->bindParam(':misyon_aciklama', $data['misyon_aciklama'], PDO::PARAM_STR);
        $guncelle_sorgu->bindParam(':iletisim_eposta', $data['iletisim_eposta'], PDO::PARAM_STR);
        $guncelle_sorgu->bindParam(':iletisim_telefon', $data['iletisim_telefon'], PDO::PARAM_STR);
        $guncelle_sorgu->bindParam(':iletisim_adres', $data['iletisim_adres'], PDO::PARAM_STR);

        try {
            $guncelle_sorgu->execute();
            echo '<div class="alert success">Bilgiler başarıyla güncellendi.</div>';
            header('Refresh:2; admin_panel.php');
        } catch (PDOException $e) {
            die("Güncelleme hatası: " . $e->getMessage());
        }
    }
}

$hakkimizda = new Hakkimizda("localhost", "root", "", "sinema");
$hakkimizda_veriler = $hakkimizda->getBilgiler();

if (!$hakkimizda_veriler) {
    die("Hakkımızda bilgileri bulunamadı.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hakkimizda->guncelle($_POST);
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hakkımızda Düzenle</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f2f2f2;
            color: #333;
            line-height: 1.6;
        }

        header {
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 20px;
            text-align: center;
        }

        h1 {
            font-size: 36px;
            margin: 0;
        }

        main {
            background-color: #ecf0f1;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 20px;
            padding: 20px;
        }

        form {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #333;
        }

        input,
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
        }

        button {
            background-color: #3498db;
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 15px;
            display: inline-block;
        }

        button:hover {
            background-color: #2980b9;
        }

        .alert {
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .success {
            background-color: #2ecc71;
            color: #fff;
        }

        footer {
            margin-top: 20px;
            background-color: #34495e;
            color: #ecf0f1;
            text-align: center;
            padding: 20px;
        }

        .back-link {
            display: inline-block;
            margin-top: 15px;
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <header>
        <h1>Hakkımızda Düzenle</h1>
    </header>

    <main>
        <section id="about">
            <form action="hakkimizda_duzenle.php" method="post">
                <label for="firma_adi">Firma Adı:</label>
                <input type="text" name="firma_adi" value="<?php echo $hakkimizda_veriler['firma_adi']; ?>" required>

                <label for="hosgeldiniz_baslik">Hoş Geldiniz Başlık:</label>
                <input type="text" name="hosgeldiniz_baslik" value="<?php echo $hakkimizda_veriler['hosgeldiniz_baslik']; ?>" required>

                <label for="vizyon_baslik">Vizyon Başlık:</label>
                <input type="text" name="vizyon_baslik" value="<?php echo $hakkimizda_veriler['vizyon_baslik']; ?>">

                <label for="vizyon_aciklama">Vizyon Açıklama:</label>
                <textarea name="vizyon_aciklama"><?php echo $hakkimizda_veriler['vizyon_aciklama']; ?></textarea>

                <label for="misyon_baslik">Misyon Başlık:</label>
                <input type="text" name="misyon_baslik" value="<?php echo $hakkimizda_veriler['misyon_baslik']; ?>">

                <label for="misyon_aciklama">Misyon Açıklama:</label>
                <textarea name="misyon_aciklama"><?php echo $hakkimizda_veriler['misyon_aciklama']; ?></textarea>

                <label for="iletisim_eposta">İletişim E-Posta:</label>
                <input type="email" name="iletisim_eposta" value="<?php echo $hakkimizda_veriler['iletisim_eposta']; ?>">

                <label for="iletisim_telefon">İletişim Telefon:</label>
                <input type="tel" name="iletisim_telefon" value="<?php echo $hakkimizda_veriler['iletisim_telefon']; ?>">

                <label for="iletisim_adres">İletişim Adres:</label>
                <textarea name="iletisim_adres"><?php echo $hakkimizda_veriler['iletisim_adres']; ?></textarea>

                <button type="submit">Bilgileri Güncelle</button>
                <a href="admin_panel.php" class="back-link">Geri</a>
            </form>
        </section>
    </main>
</body>

</html>
