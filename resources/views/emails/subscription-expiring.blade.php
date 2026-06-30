<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Predplatné čoskoro vyprší</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e91e63;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #e91e63;
            margin-bottom: 10px;
        }
        .warning-icon {
            font-size: 48px;
            color: #ff9800;
            margin-bottom: 20px;
        }
        .title {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .expiry-details {
            background-color: #fff3cd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #ff9800;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .detail-value {
            color: #333;
        }
        .urgent {
            background-color: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }
        .button {
            display: inline-block;
            background-color: #e91e63;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button-urgent {
            background-color: #dc3545;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Erotikon.sk</div>
            <div class="warning-icon">⚠️</div>
            <h1 class="title">
                @if($daysLeft == 1)
                    Predplatné vyprší zajtra!
                @else
                    Predplatné čoskoro vyprší
                @endif
            </h1>
        </div>

        <p>Dobrý deň {{ $user->name }},</p>

        <p>toto je pripomienka, že vaše predplatné na našej platforme sa končí dňa {{ $ad->subscription_expires_at->format('d.m.Y') }}. Ak chcete pokračovať v používaní našich služieb bez prerušenia, prosím obnovte svoje predplatné čo najskôr. Môžete to urobiť prostredníctvom svojho profilu na našej stránke.</p>

        <div class="expiry-details {{ $daysLeft <= 1 ? 'urgent' : '' }}">
            <h3 style="margin-top: 0; color: {{ $daysLeft <= 1 ? '#dc3545' : '#ff9800' }};">
                Detaily predplatného
            </h3>
            
            <div class="detail-row">
                <span class="detail-label">Inzerát:</span>
                <span class="detail-value">#{{ $ad->id }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Predplatné vyprší:</span>
                <span class="detail-value">{{ $ad->subscription_expires_at->format('d.m.Y H:i') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Zostáva:</span>
                <span class="detail-value">
                    @if($daysLeft == 1)
                        <strong style="color: #dc3545;">Menej ako 1 deň</strong>
                    @else
                        <strong style="color: #ff9800;">{{ $daysLeft }} dní</strong>
                    @endif
                </span>
            </div>
        </div>

        <p>
            @if($daysLeft == 1)
                <strong>Ak chcete, aby váš inzerát zostal aktívny, predĺžte si predplatné ešte dnes!</strong>
            @else
                Ak chcete, aby váš inzerát zostal aktívny, odporúčame vám predĺžiť si predplatné čo najskôr.
            @endif
        </p>

        <p>Po vypršaní predplatného sa váš inzerát automaticky deaktivuje a nebude sa zobrazovať na našej stránke.</p>

        <div style="text-align: center;">
            <a href="{{ route('ads.payment.packages', $ad->id) }}" 
               class="button {{ $daysLeft <= 1 ? 'button-urgent' : '' }}">
                Predĺžiť predplatné teraz
            </a>
        </div>

        <p><strong>S pozdravom,<br>
        Tím Erotikon.sk</strong></p>

        <div class="footer">
            <p>Toto je automaticky generovaný email. Neodpovedajte na túto správu.</p>
            <p>© {{ date('Y') }} Erotikon.sk - Všetky práva vyhradené</p>
            <p>
                <a href="{{ route('home') }}" style="color: #e91e63;">Navštíviť stránku</a> | 
                <a href="{{ route('support.index') }}" style="color: #e91e63;">Podpora</a>
            </p>
        </div>
    </div>
</body>
</html> 