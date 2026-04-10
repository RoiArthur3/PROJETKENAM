<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Image - {{ $file->nom }}</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: 'Helvetica', 'Arial', sans-serif;
            text-align: center;
        }
        .header {
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .file-info {
            margin-bottom: 20px;
            color: #666;
            font-size: 14px;
        }
        .image-container {
            max-width: 100%;
            margin: 0 auto;
        }
        .image-container img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            padding: 5px;
            background: #fff;
        }
        .footer {
            margin-top: 50px;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Document - Opérations KENAM</h2>
    </div>

    <div class="file-info">
        <strong>Fichier :</strong> {{ $file->nom }}<br>
        <strong>Opération # :</strong> {{ $file->operation_id }}<br>
        <strong>Date d'export :</strong> {{ date('d/m/Y H:i') }}
    </div>

    <div class="image-container">
        @php
            $path = storage_path('app/public/' . $file->chemin);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            } else {
                $base64 = null;
            }
        @endphp

        @if($base64)
            <img src="{{ $base64 }}" alt="{{ $file->nom }}">
        @else
            <p style="color: red;">Image non trouvée sur le serveur.</p>
        @endif
    </div>

    <div class="footer">
        Système de Gestion KENAM Services &copy; {{ date('Y') }}
    </div>
</body>
</html>
