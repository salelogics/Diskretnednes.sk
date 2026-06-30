<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Váš inzerát bol vytvorený - Erotikon.sk</title>
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
            margin-bottom: 10px;
        }
        .ad-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #e91e63;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
        }
        .detail-label { font-weight: bold; color: #555; }
        .detail-value { color: #333; }
        .next-steps {
            background-color: #fff3cd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
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
        .button-secondary { background-color: #2196f3; margin-left: 10px; }
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
            <h1 class="title">Inzerát úspešne vytvorený!</h1>
        </div>

        <p>Dobrý deň <strong>{{ $user->name }}</strong>,</p>
        <p>Váš inzerát bol úspešne pridaný na Erotikon.sk. Môžete ho kedykoľvek upraviť alebo spravovať vo svojom používateľskom účte.</p>

        <div class="ad-details">
            <h3 style="margin-top: 0; color: #e91e63;">Detaily inzerátu</h3>
            <div class="detail-row">
                <span class="detail-label">ID inzerátu:</span>
                <span class="detail-value">#{{ $ad->id }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Prezývka:</span>
                <span class="detail-value">{{ $ad->nickname ?: 'Nezadané' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Typ:</span>
                <span class="detail-value">{{ $ad->ad_type_label }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Mesto:</span>
                <span class="detail-value">{{ $ad->city_label }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Vytvorený:</span>
                <span class="detail-value">{{ $ad->created_at->format('d.m.Y H:i') }}</span>
            </div>
        </div>

        @if($ad->status === 'draft')
        <div class="next-steps">
            <h3 style="margin-top: 0; color: #856404;">Ďalšie kroky</h3>
            <ul style="margin: 0; padding-left: 18px;">
                <li>Dokončite a aktivujte inzerát</li>
                <li>Pridajte kvalitné fotky</li>
                <li>Overte telefónne číslo</li>
                <li>Zvážte predplatné pre lepšiu viditeľnosť</li>
            </ul>
        </div>
        @endif

        <div style="text-align: center;">
            <a href="{{ route('ads.index') }}" class="button">Spravovať inzeráty</a>
            @if($ad->status === 'draft')
                <a href="{{ route('ads.edit', $ad->id) }}" class="button button-secondary">Upraviť inzerát</a>
            @else
                <a href="{{ route('ads.statistics', $ad->id) }}" class="button button-secondary">Zobraziť štatistiky</a>
            @endif
        </div>

        <p><strong>S pozdravom,<br>Tím Erotikon.sk</strong></p>

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