<?php
// Mengimpor kelas-kelas PHPMailer ke dalam namespace global
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// KARENA TIDAK MENGGUNAKAN COMPOSER, GANTI 'require vendor/autoload.php'
// DENGAN TIGA BARIS BERIKUT:
require 'PHPMailer-6.10.0/src/Exception.php';
require 'PHPMailer-6.10.0/src/PHPMailer.php';
require 'PHPMailer-6.10.0/src/SMTP.php';

// Mengatur header respons sebagai JSON
header('Content-Type: application/json');

// Memeriksa apakah request adalah metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Mengambil dan membersihkan data dari form
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Validasi dasar
    if (empty($name) || empty($email) || empty($subject) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Harap isi semua kolom dengan benar.']);
        exit;
    }

    // Membuat instance PHPMailer baru
    $mail = new PHPMailer(true);

    try {
        // Pengaturan Server (SMTP)
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Aktifkan untuk debugging mendetail
        $mail->isSMTP();                                            // Menggunakan SMTP
        $mail->Host       = 'smtp.gmail.com';                       // Ganti dengan server SMTP Anda (contoh: smtp.gmail.com)
        $mail->SMTPAuth   = true;                                   // Mengaktifkan otentikasi SMTP
        $mail->Username   = 'ullulazmia.l@gmail.com';                 // Ganti dengan alamat email SMTP Anda
        $mail->Password   = 'jhfm anfi ogeb izaj';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Mengaktifkan enkripsi TLS implisit
        $mail->Port       = 465;                                    // Port TCP untuk SMTPS

        // Penerima Email
        $mail->setFrom($email, $name); // Email pengirim diambil dari form
        $mail->addAddress('ullulazmia.l@gmail.com', 'Ulul Azmi A. Latala'); // GANTI DENGAN EMAIL TUJUAN ANDA
        $mail->addReplyTo($email, $name); // Agar saat membalas, langsung ke email pengirim

        // Konten Email
        $mail->isHTML(true); // Mengatur format email sebagai HTML
        $mail->Subject = 'Pesan Baru dari Portofolio: ' . $subject;

        // Body email dalam bentuk HTML yang lebih rapi
        $mail->Body    = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; }
                    .container { padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
                    .header { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
                    .field { margin-bottom: 10px; }
                    .field span { font-weight: bold; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>Anda menerima pesan baru dari form kontak portofolio Anda.</div>
                    <div class='field'><span>Nama:</span> " . $name . "</div>
                    <div class='field'><span>Email:</span> " . $email . "</div>
                    <div class='field'><span>Subjek:</span> " . $subject . "</div>
                    <div class='field'><span>Pesan:</span></div>
                    <div>" . nl2br($message) . "</div>
                </div>
            </body>
            </html>
        ";

        // Alternatif body untuk klien email non-HTML
        $mail->AltBody = "Nama: $name\nEmail: $email\nSubjek: $subject\nPesan:\n$message";

        $mail->send();
        echo json_encode(['status' => 'success', 'message' => 'Pesan Anda berhasil terkirim. Terima kasih!']);
    } catch (Exception $e) {
        // Mengirim respons error jika gagal
        echo json_encode(['status' => 'error', 'message' => "Pesan gagal terkirim. Mailer Error: {$mail->ErrorInfo}"]);
    }
} else {
    // Jika bukan metode POST
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak valid.']);
}
