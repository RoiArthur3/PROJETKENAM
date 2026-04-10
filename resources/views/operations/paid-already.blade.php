<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Opération déjà payée</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; color: #333; }
        .container { max-width: 500px; margin: 40px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #eee; padding: 32px; text-align: center; }
        .info { color: #4e73df; font-size: 32px; margin-bottom: 16px; }
        .details { margin: 20px 0; }
        .btn { display: inline-block; margin-top: 24px; background: #1cc88a; color: #fff; padding: 10px 24px; border-radius: 5px; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="info">ℹ️</div>
        <h2>Opération déjà payée</h2>
        <div class="details">
            <p>L'opération <strong>#{{ $operation->id }} - {{ $operation->titre }}</strong> a déjà été marquée comme <strong>payée</strong>.</p>
        </div>
        <a href="{{ route('operations.show', $operation->id) }}" class="btn">Voir l'opération</a>
    </div>
</body>
</html>
