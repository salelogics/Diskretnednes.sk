<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platba úspešne spracovaná</title>
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
        .success-icon {
            font-size: 48px;
            color: #4caf50;
            margin-bottom: 20px;
        }
        .title {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .payment-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #4caf50;
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
            <div class="success-icon">✅</div>
            <h1 class="title">Platba úspešne spracovaná!</h1>
        </div>

        <p>Dobrý deň {{ $user->name }},</p>

        <p>Vaša platba bola úspešne spracovaná a predplatné vášho inzerátu je teraz aktívne.</p>

        <div class="payment-details">
            <h3 style="margin-top: 0; color: #4caf50;">Detaily platby</h3>
            
            <div class="detail-row">
                <span class="detail-label">Číslo platby:</span>
                <span class="detail-value">#{{ $payment->payment_id }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Suma:</span>
                <span class="detail-value">{{ $payment->formatted_amount }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Inzerát:</span>
                <span class="detail-value">#{{ $payment->ad_id }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Doba predplatného:</span>
                <span class="detail-value">{{ $payment->duration_label }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Platné do:</span>
                <span class="detail-value">{{ $payment->subscription_ends_at->format('d.m.Y H:i') }}</span>
            </div>
            
            @if($payment->is_featured)
            <div class="detail-row">
                <span class="detail-label">Zvýraznený inzerát:</span>
                <span class="detail-value">✅ Áno</span>
            </div>
            @endif
            
            @if($payment->is_top_ad)
            <div class="detail-row">
                <span class="detail-label">TOP inzerát:</span>
                <span class="detail-value">✅ Áno</span>
            </div>
            @endif
        </div>

        <p>Váš inzerát je teraz aktívny a zobrazuje sa na našej stránke. Môžete si pozrieť štatistiky a spravovať svoj inzerát v používateľskom účte.</p>

        <div style="text-align: center;">
            <a href="{{ route('ads.index') }}" class="button">Zobraziť moje inzeráty</a>
        </div>

        <p>Ďakujeme za využívanie našich služieb!</p>

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