<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platobné inštrukcie - Erotikon.sk</title>
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
            border-bottom: 2px solid #2196f3;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2196f3;
            margin-bottom: 10px;
        }
        .payment-icon {
            font-size: 48px;
            color: #2196f3;
            margin-bottom: 20px;
        }
        .title {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .payment-details {
            background-color: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #2196f3;
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
            font-weight: bold;
        }
        .highlight {
            background-color: #ffeb3b;
            padding: 2px 6px;
            border-radius: 3px;
        }
        .button {
            display: inline-block;
            background-color: #2196f3;
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
            <div class="payment-icon">💳</div>
            <h1 class="title">Platobné inštrukcie</h1>
        </div>

        <p>Dobrý deň <strong>{{ $user->name }}</strong>,</p>

        <p>ďakujeme za váš záujem o našu službu. Na dokončenie platby použite nasledujúce údaje:</p>

        <div class="payment-details">
            <h3 style="margin-top: 0; color: #2196f3;">Platobné údaje</h3>
            
            <div class="detail-row">
                <span class="detail-label">Číslo účtu:</span>
                <span class="detail-value">5187899863</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">IBAN:</span>
                <span class="detail-value">SK77 0900 0000 0051 8789 9863</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Variabilný symbol:</span>
                <span class="detail-value highlight">{{ $payment->payment_id ?? $payment->id }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Suma:</span>
                <span class="detail-value highlight">{{ number_format($payment->amount, 2) }} EUR</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Dátum splatnosti:</span>
                <span class="detail-value">{{ now()->addDays(7)->format('d.m.Y') }}</span>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('ads.payment.status', $payment->payment_id ?? $payment->id) }}" class="button">
                Skontrolovať stav platby
            </a>
        </div>

        <p>Ak máte akékoľvek otázky alebo potrebujete pomoc, neváhajte nás kontaktovať.</p>

        <p><strong>S pozdravom,<br>
        Tím Erotikon.sk</strong></p>

        <div class="footer">
            <p>Toto je automaticky generovaný email. Neodpovedajte na túto správu.</p>
            <p>© {{ date('Y') }} Erotikon.sk - Všetky práva vyhradené</p>
            <p>
                <a href="{{ route('home') }}" style="color: #2196f3;">Navštíviť stránku</a> | 
                <a href="{{ route('support.index') }}" style="color: #2196f3;">Podpora</a>
            </p>
        </div>
    </div>
</body>
</html> 
 