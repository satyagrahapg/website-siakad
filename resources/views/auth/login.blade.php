<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="{{asset('style/assets/logo-sekolah.png')}}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('style/css/login_style.css') }}">
    <script src="https://kit.fontawesome.com/9d2abd8931.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .footer {
            padding: 5px 0;
            /* Reduces vertical padding */
            background-color: #00BFFF;
            /* Optional: Set background color */
            text-align: center;
            /* Centers the text */
        }

        .footer p {
            margin: 0;
            /* Removes extra margin around paragraph */
            font-size: 12px;
            /* Adjust font size as needed */
            color: #ffffff;
            /* Optional: Adjust text color */
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo-section">
            <img src="{{asset('style/assets/logo-sekolah.png')}}" alt="Logo">
            <h1>Sistem Informasi Akademik</h1>
            <h2>SMP Negeri 1 Karangawen</h2>
        </div>
        <div class="form-section">
            <p class="intro">Selamat Datang!</p>
            <p style="margin-bottom: 10px;">Masukkan Nama Pengguna dan Kata Sandi untuk masuk.</p>

            @if ($errors->any())
            <div style="color: red; font-size: 14px;">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('post_login') }}" method="POST">
                @csrf
                <div class="form-group" style="margin-top: 10px;">
                    <label for="username">Nama Pengguna</label>
                    <input class="form-control" type="text" id="username" name="username" placeholder="Masukkan Nama Pengguna" required>
                </div>
                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <input class="form-control" type="password" id="password" name="password" placeholder="Masukkan Kata Sandi" required>
                </div>
                <button type="submit">Masuk</button>
            </form>

            <div style='--bs-gutter-x: 0rem !important;' class="form-group row">
                <div class="col-md-6 offset-md-4">
                    <div class="checkbox">
                        <label>
                            <a href="{{ route('forget.password.get') }}">Lupa Kata Sandi</a>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
        <p>&copy; Dibuat oleh TIM TA Satyagraha x Rizqi | 2025 | Teknik Elektro Universitas Diponegoro</p>
    </div>
</body>

</html>