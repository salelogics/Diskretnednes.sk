<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inzerát aktualizovaný - DiskretneDnes.sk</title>
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
            border-bottom: 2px solid #4caf50;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #4caf50;
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
        .update-content {
            background-color: #e8f5e8;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #4caf50;
        }
        .button {
            display: inline-block;
            background-color: #4caf50;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button-secondary {
            background-color: #2196f3;
            margin-left: 10px;
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
            <div class="logo">DiskretneDnes.sk</div>
            <div class="success-icon">✅</div>
            <h1 class="title">Inzerát aktualizovaný!</h1>
        </div>

        <div class="update-content">
            <p>Dobrý deň <strong>{{ $user->name }}</strong>,</p>
            <p>váš inzerát bol úspešne aktualizovaný. Môžete ho skontrolovať vo svojom profile. Ďakujeme, že používate našu službu!</p>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('ads.index') }}" class="button">Spravovať inzeráty</a>
            <a href="{{ route('ad.show', $ad->id) }}" class="button button-secondary">Zobraziť inzerát</a>
        </div>

        <p>Ak máte akékoľvek otázky ohľadom vášho inzerátu, neváhajte nás kontaktovať cez náš support systém.</p>

        <p><strong>S pozdravom,<br>
        Tím DiskretneDnes.sk</strong></p>

        <div class="footer">
            <p>Toto je automaticky generovaný email. Neodpovedajte na túto správu.</p>
            <p>© {{ date('Y') }} DiskretneDnes.sk - Všetky práva vyhradené</p>
            <p>
                <a href="{{ route('home') }}" style="color: #4caf50;">Navštíviť stránku</a> | 
                <a href="{{ route('support.index') }}" style="color: #4caf50;">Podpora</a> |
                <a href="{{ route('ads.index') }}" style="color: #4caf50;">Moje inzeráty</a>
            </p>
        </div>
    </div>
</body>
</html> 