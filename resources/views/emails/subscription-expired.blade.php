<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Predplatné vypršalo - DiskretneDnes.sk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #dc3545;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #e91e63;
            margin-bottom: 10px;
        }
        .expired-icon {
            font-size: 64px;
            color: #dc3545;
            margin-bottom: 15px;
        }
        .title {
            color: #dc3545;
            font-size: 26px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .subtitle {
            color: #6c757d;
            font-size: 16px;
        }
        .ad-details {
            background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
            padding: 20px;
            border-radius: 10px;
            margin: 25px 0;
            border-left: 5px solid #dc3545;
        }
        .stats-box {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            padding: 20px;
            border-radius: 10px;
            margin: 25px 0;
            text-align: center;
            border-left: 5px solid #2196f3;
        }
        .views-count {
            font-size: 32px;
            font-weight: bold;
            color: #1976d2;
            margin-bottom: 5px;
        }
        .payment-section {
            background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);
            padding: 25px;
            border-radius: 10px;
            margin: 25px 0;
            border-left: 5px solid #9c27b0;
        }
        .payment-methods {
            margin: 20px 0;
        }
        .payment-method {
            margin-bottom: 15px;
            padding: 10px;
            background: rgba(255,255,255,0.7);
            border-radius: 8px;
        }
        .bank-details {
            background: linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%);
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 5px solid #ff9800;
        }
        .bank-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        .bank-detail {
            background: rgba(255,255,255,0.8);
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ffcc02;
        }
        .bank-name {
            font-weight: bold;
            color: #e65100;
            font-size: 16px;
            margin-bottom: 10px;
            text-align: center;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 3px 0;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
            min-width: 120px;
        }
        .detail-value {
            color: #333;
            font-weight: 600;
        }
        .highlight-amount {
            background: #ffeb3b;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            color: #e65100;
        }
        .warning-box {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .warning-title {
            color: #856404;
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .benefits-section {
            background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
            padding: 25px;
            border-radius: 10px;
            margin: 25px 0;
            border-left: 5px solid #4caf50;
        }
        .benefits-list {
            list-style: none;
            padding: 0;
        }
        .benefits-list li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
        }
        .benefits-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #4caf50;
            font-weight: bold;
            font-size: 16px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #e91e63 0%, #ad1457 100%);
            color: white;
            padding: 15px 35px;
            text-decoration: none;
            border-radius: 8px;
            margin: 15px 10px;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(233, 30, 99, 0.3);
            transition: all 0.3s ease;
        }
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(233, 30, 99, 0.4);
        }
        .button-secondary {
            background: linear-gradient(135deg, #2196f3 0%, #1565c0 100%);
            box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3);
        }
        .footer {
            margin-top: 40px;
            padding-top: 25px;
            border-top: 2px solid #eee;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .contact-info {
            background: linear-gradient(135deg, #f5f5f5 0%, #eeeeee 100%);
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: center;
        }
        @media (max-width: 600px) {
            .bank-info {
                grid-template-columns: 1fr;
            }
            .button {
                display: block;
                margin: 10px 0;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">DiskretneDnes.sk</div>
            <div class="expired-icon">⛔</div>
            <h1 class="title">Predplatné vypršalo</h1>
            <p class="subtitle">Váš inzerát sa NEZOBRAZUJE</p>
        </div>

        <p><strong>Vážený používateľ,</strong></p>

        <div class="ad-details">
            <h3 style="margin-top: 0; color: #dc3545; font-size: 18px;">
                📋 Vypršalo predplatné Vášho inzerátu č. {{ $ad->id }}
            </h3>
            <p style="margin-bottom: 0; font-size: 16px;">
                <strong>Inzerát sa NEZOBRAZUJE</strong> - je potrebné predplatné obnoviť
            </p>
        </div>

        <div class="stats-box">
            <div class="views-count">{{ number_format($ad->views ?? 173856) }}</div>
            <p style="margin: 0; color: #1976d2; font-weight: 600;">
                👥 návštevníkov si otvorilo Váš inzerát
            </p>
        </div>

        <p>Ak máte záujem pokračovať v inzercií, je potrebné inzerát predplatiť na ďalšie obdobie. Stačí keď sa prihlásite, kliknete na tlačidlo <strong>"predplatiť"</strong>, vyberiete typ predplatného a dokončíte objednávku.</p>

        <div class="payment-section">
            <h3 style="margin-top: 0; color: #7b1fa2; font-size: 18px;">
                💳 Podporujeme nasledovné možnosti platby:
            </h3>
            <div class="payment-methods">
                <div class="payment-method">
                    <strong>📱 Platba mobilom</strong> prostredníctvom spoplatnenej SMS správy
                </div>
                <div class="payment-method">
                    <strong>🏦 Bankový prevod</strong> alebo vklad v hotovosti vo FIO banke
                </div>
                <div class="payment-method">
                    <strong>💻 Online platba</strong> prostredníctvom platobnej brány STRIPE
                </div>
            </div>
        </div>

        <div class="bank-details">
            <h3 style="margin-top: 0; color: #e65100; font-size: 18px;">
                🏛️ Údaje potrebné pre úhradu predplatného (25 dní CLASSIC):
            </h3>
            
            <div class="bank-info">
                <div class="bank-detail">
                    <div class="bank-name">Fio banka, a.s</div>
                    <div class="detail-row">
                        <span class="detail-label">IBAN:</span>
                        <span class="detail-value">SK77 0900 0000 0051 8789 9863</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Číslo účtu:</span>
                        <span class="detail-value">5187899863</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Variabilný symbol:</span>
                        <span class="detail-value">{{ $ad->id }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Konštantný symbol:</span>
                        <span class="detail-value">{{ $ad->id }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Suma:</span>
                        <span class="detail-value highlight-amount">{{ $ad->subscription_package ?? '40' }} EUR</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="warning-box">
            <div class="warning-title">⚠️ Dôležité upozornenia:</div>
            <ul style="margin: 0; padding-left: 20px;">
                <li><strong>Vždy uvádzajte správny variabilný symbol</strong> - bez neho sa platba nespracuje</li>
                <li><strong>Každá banka si účtuje poplatok</strong> za vklad v hotovosti priamo v banke</li>
                <li><strong>Bankový prevod v rámci rovnakej banky</strong> sa realizuje do niekoľkých minút</li>
                <li><strong>Medzibankový prevod</strong> trvá minimálne 1 pracovný deň</li>
            </ul>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('ads.index') }}" class="button">
                🔄 Aktivovať znova zadarmo
            </a>
        </div>

        <div class="benefits-section">
            <h3 style="margin-top: 0; color: #2e7d32; font-size: 18px;">
                🌟 Prečo inzerovať na DiskretneDnes.sk?
            </h3>
            <ul class="benefits-list">
                <li>Sme nová jednotka v erotickej inzercií na Slovensku</li>
                <li>Denne nás navštívi v priemere viac ako <strong>70 000 návštevníkov</strong></li>
                <li>Vaše inzeráty mesačne navštívi viac ako <strong>2 200 000 ľudí</strong></li>
                <li>Aktívne inzerujeme a neustále hľadáme nové možnosti propagácie</li>
                <li>Jednoduchosť, prehľadnosť a efektivita inzercie je pre nás priorita</li>
                <li>Stránky sú prispôsobené pre akékoľvek zariadenie (mobil, tablet, PC)</li>
                <li>Profesionalita, flexibilita a technická podpora je samozrejmosťou</li>
            </ul>
        </div>

        <div class="contact-info">
            <p style="margin: 0; font-weight: 600; color: #333;">
                📞 V prípade otázok alebo nejasností nás neváhajte kontaktovať
            </p>
            <p style="margin: 5px 0 0 0; color: #666;">
                📧 info@diskretnednes.sk
            </p>
        </div>

        <p style="text-align: center; margin: 30px 0; font-size: 16px;">
            <strong>Ďakujeme a prajeme príjemný deň.</strong><br>
            <span style="color: #e91e63; font-weight: 600;">Váš tím DiskretneDnes.sk</span>
        </p>

        <div class="footer">
            <p>Toto je automaticky generovaný email. Neodpovedajte na túto správu.</p>
            <p>© {{ date('Y') }} DiskretneDnes.sk - Všetky práva vyhradené</p>
            <p>
                <a href="{{ route('home') }}" style="color: #e91e63;">🏠 Navštíviť stránku</a> | 
                <a href="{{ route('support.index') }}" style="color: #e91e63;">💬 Podpora</a> |
                <a href="{{ route('ads.index') }}" style="color: #e91e63;">📋 Moje inzeráty</a>
            </p>
        </div>
    </div>
</body>
</html> 