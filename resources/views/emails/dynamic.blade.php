<!DOCTYPE html>
<html>
<head>
    <title>{{ $subject ?? 'Notification' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 15px 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #e1e1e1;
            border-top: none;
        }
        .footer {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
            font-size: 0.9em;
            color: #666;
            text-align: center;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 15px 0;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .details {
            margin: 1.5rem 0;
            padding: 1rem;
            background: #f5f5f5;
            border-radius: 4px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>KENAM SERVICES</h1>
    </div>

    <div class="content">
        {!! nl2br(e($content)) !!}

        @if(isset($data['details']))
        <div class="details">
            {!! nl2br(e($data['details'])) !!}
        </div>
        @endif

        @if(isset($data['action_url']) && isset($data['action_text']))
        <div style="text-align: center; margin: 25px 0;">
            <a href="{{ $data['action_url'] }}" class="button">
                {{ $data['action_text'] }}
            </a>
        </div>
        @endif
    </div>

    @if(isset($data['footer']))
    <div class="footer">
        {!! $data['footer'] !!}
    </div>
    @else
    <div class="footer">
        <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
        <p>&copy; {{ date('Y') }} KENAM SERVICES. Tous droits réservés.</p>
    </div>
    @endif
</body>
</html>
