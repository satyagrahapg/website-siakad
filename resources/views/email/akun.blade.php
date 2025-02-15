<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun SMP Anda Telah Dibuat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            padding: 20px;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #4CAF50;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h2>Halo {{ $username }},</h2>
        <p>Selamat! Akun SIAKAD Anda telah berhasil dibuat. Berikut adalah informasi akun Anda:</p>
        
        <p><strong>Nama Pengguna:</strong> {{ $username }}</p>
        <p><strong>Kata Sandi:</strong> {{ $password }}</p>
        
        <p>Silakan gunakan informasi ini untuk masuk ke akun Anda dan mulai mengakses layanan SIAKAD.</p>

        <div class="footer">
            <p>Jika Anda mengalami masalah, hubungi Admin Sekolah.</p>
            <p>Terima kasih!</p>
        </div>
    </div>
</body>
</html>
