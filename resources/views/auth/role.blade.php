<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role</title>
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

    <style>
        /* Grid untuk menampilkan role */
        .role-grid {
            display: grid;
            padding: 0 40px;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); /* Kolom otomatis */
            gap: 20px; /* Jarak antar kotak */
            justify-items: center; /* Pusatkan grid */
            box-sizing: border-box; /* Pastikan padding dihitung dalam ukuran grid */
        }

        /* Style untuk custom radio */
        .custom-radio {
            position: relative;
            display: block;
            text-align: center;
            cursor: pointer;
        }

        /* Sembunyikan radio asli */
        .custom-radio input[type="radio"] {
            display: none;
        }

        /* Kotak role */
        .role-box {
            width: 120px;
            height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border: 2px solid transparent;
            border-radius: 10px; /* Opsional: Membuat kotak dengan sudut membulat */
            background-color: #f9f9f9;
            transition: border-color 0.3s, background-color 0.3s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Memberikan efek bayangan */
        }

        .role-box:hover {
            background-color: #f1f1f1; /* Warna latar belakang lebih gelap saat hover */
            border-color: #00bfff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Bayangan lebih jelas */
        }

        /* Gambar role */
        .role-icon {
            width: 50px;
            height: 50px;
            margin-bottom: 10px;
        }

        /* Nama role */
        .role-box span {
            font-size: 14px;
            color: #333;
            text-align: center;
            word-wrap: break-word; /* Memastikan teks panjang tidak melanggar batas */
        }

        /* Kotak aktif */
        .custom-radio input[type="radio"]:checked + .role-box {
            border-color: #00bfff;
            background-color: #b6eafb; /* Warna hijau muda */
        }

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
            <p class="intro">Pilih Hak Akses</p>
            <p style="margin-bottom: 30px">Pilih hak akses yang ingin anda gunakan untuk masuk.</p>
            @if (count($roles) > 0)
            <form id="roleForm" action="{{ route('post_role') }}" method="POST" style="justify-items: center">
                @csrf
                <div class="row" style="width: 18rem;">
                    @foreach ($roles as $role)
                        <div class="col" style="justify-items: center; margin-bottom: 20px;">
                            <label class="custom-radio">
                                <input 
                                    type="radio" 
                                    name="role" 
                                    value="{{ $role }}"
                                    onchange="document.getElementById('roleForm').submit();"
                                >
                                <div class="role-box">
                                    <div style="margin-top: 20px;">
                                        @if ($role === 'Super Admin')
                                            <i class="fa-solid fa-user-gear fa-2xl" style="font-size: 40px;"></i>
                                        @elseif ($role === 'Admin')
                                            <i class="fa-solid fa-users-gear fa-2xl fa-solid fa-user-gear fa-2xl" style="font-size: 40px;"></i>
                                        @elseif ($role === 'Guru')
                                            <i class="fa-solid fa-chalkboard-user fa-2xl" style="font-size: 40px;"></i>
                                        @elseif ($role === 'Wali Kelas')
                                            <i class="fa-solid fa-user-group fa-2xl" style="font-size: 40px;"></i>
                                        @endif
                                        <p style="margin-top: 15px;">{{ $role }}</p>
                                    </div>
                                </div>
                            </label>
                        </div>                        
                    @endforeach
                </div>
            </form>
            @else
                <div class="row" style="width: 22rem; height: 16rem; align-items: center;">
                    <div class="col" style="justify-items: center">
                        <h5 style="text-align: center; color: red;">Akun Anda saat ini tidak memiliki role yang tersedia. Silahkan hubungi admin akun sekolah.</h5>
                        <form action={{ route('logout') }} method="POST" onsubmit="event.stopPropagation();">
                            @csrf
                            <button type="submit" class="btn btn-danger" style="width: 8rem;">Keluar</button>
                        </form>
                    </div>
                </div>
            @endif
                
        </div>
    </div>
    <div class="footer">
        <p class="footer">&copy; Dibuat oleh TIM TA Satyagraha x Rizqi | 2024/2025 | SMP Negeri 1 Karangawen x Universitas Diponegoro</p>
    </div>
</body>

</html>