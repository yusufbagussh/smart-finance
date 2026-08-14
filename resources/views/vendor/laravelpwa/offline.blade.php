<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline | Artafis Smart Finance</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            background-color: #0f172a;
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 24px;
            padding: 40px 32px;
            max-width: 440px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .icon-container {
            width: 88px;
            height: 88px;
            background: rgba(79, 70, 229, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px auto;
            position: relative;
        }

        .icon-container svg {
            width: 44px;
            height: 44px;
            color: #818cf8;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            font-size: 13px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 9999px;
            margin-bottom: 20px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background-color: #ef4444;
            border-radius: 50%;
        }

        h1 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #ffffff;
            letter-spacing: -0.02em;
        }

        p {
            font-size: 14px;
            line-height: 1.6;
            color: #94a3b8;
            margin-bottom: 32px;
        }

        .btn-retry {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            background-color: #4f46e5;
            color: #ffffff;
            font-size: 15px;
            font-weight: 600;
            padding: 14px 20px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-retry:hover {
            background-color: #4338ca;
            transform: translateY(-1px);
        }

        .btn-retry:active {
            transform: translateY(0);
        }

        .btn-retry svg {
            width: 18px;
            height: 18px;
        }

        .footer-note {
            margin-top: 24px;
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>

<body>

    <div class="card">
        <!-- Visual Icon: Cloud Offline & Finance Security -->
        <div class="icon-container">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4l16 16" />
            </svg>
        </div>

        <div class="status-badge">
            <span class="status-dot"></span>
            Koneksi Terputus
        </div>

        <h1>Sinkronisasi Finansial Tertunda</h1>
        <p>
            Artafis tidak dapat menghubungi server keuangan cerdas. Periksa jaringan data atau Wi-Fi Anda untuk
            memperbarui data transaksi dan analitik.
        </p>

        <button class="btn-retry" onclick="window.location.reload();">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Coba Sinkron Ulang
        </button>

        <div class="footer-note">
            Artafis &bull; Smart Finance Tracker
        </div>
    </div>

</body>

</html>
