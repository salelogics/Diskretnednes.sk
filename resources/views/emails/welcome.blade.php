<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vitajte na Erotikon.sk</title>
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
        .welcome-icon {
            font-size: 48px;
            color: #4caf50;
            margin-bottom: 20px;
        }
        .title {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .welcome-content {
            background-color: #e8f5e8;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #4caf50;
        }
        .feature-list {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            background-color: white;
            border-radius: 6px;
            border-left: 3px solid #e91e63;
        }
        .feature-icon {
            font-size: 20px;
            margin-right: 15px;
            color: #e91e63;
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
        .button-secondary {
            background-color: #2196f3;
            margin-left: 10px;
        }
        .tips-section {
            background-color: #fff3cd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
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
            <div class="welcome-icon">🎉</div>
            <h1 class="title">Vitajte na Erotikon.sk!</h1>
        </div>

        <div class="welcome-content">
            <h3 style="margin-top: 0; color: #4caf50;">Váš účet bol úspešne vytvorený</h3>
            <p style="margin-bottom: 0;">
                Dobrý deň <strong>{{ $user->name }}</strong>,<br><br>
                s radosťou vás vítame na našej platforme! Vaša registrácia bola úspešná. Môžete sa prihlásiť pomocou vášho používateľského mena a hesla a začať využívať všetky funkcie našej stránky. Ak máte akékoľvek otázky alebo potrebujete pomoc, neváhajte nás kontaktovať.
            </p>
        </div>

        <div class="feature-list">
            <h3 style="margin-top: 0; color: #2c3e50;">Čo môžete robiť na Erotikon.sk:</h3>
            
            <div class="feature-item">
                <div class="feature-icon">📝</div>
                <div>
                    <strong>Vytvárať inzeráty</strong><br>
                    <span style="color: #666; font-size: 14px;">Pridajte svoj inzerát s fotkami a popisom</span>
                </div>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">⭐</div>
                <div>
                    <strong>Zvýrazňovať inzeráty</strong><br>
                    <span style="color: #666; font-size: 14px;">Získajte viac pozornosti s prémiovými funkciami</span>
                </div>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">📊</div>
                <div>
                    <strong>Sledovať štatistiky</strong><br>
                    <span style="color: #666; font-size: 14px;">Monitorujte zobrazenia a kliky na vaše inzeráty</span>
                </div>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">💬</div>
                <div>
                    <strong>Podporu 24/7</strong><br>
                    <span style="color: #666; font-size: 14px;">Náš tím je tu pre vás kedykoľvek potrebujete pomoc</span>
                </div>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('dashboard') }}" class="button">Prejsť na nástenku</a>
            <a href="{{ route('ads.create') }}" class="button button-secondary">Vytvoriť inzerát</a>
        </div>

        <div class="tips-section">
            <h3 style="margin-top: 0; color: #856404;">💡 Tipy pre úspešný inzerát:</h3>
            <ul style="margin-bottom: 0; padding-left: 20px;">
                <li>Pridajte kvalitné fotky - inzeráty s fotkami majú 10x viac zobrazení</li>
                <li>Napíšte podrobný popis svojich služieb</li>
                <li>Aktualizujte pravidelne svoje hodiny dostupnosti</li>
                <li>Overte si telefónne číslo pre vyššiu dôveryhodnosť</li>
                <li>Zvážte predplatné pre lepšiu viditeľnosť</li>
            </ul>
        </div>

        <p>Ak máte akékoľvek otázky, neváhajte nás kontaktovať cez náš support systém.</p>

        <p><strong>S pozdravom,<br>
        Tím Erotikon.sk</strong></p>

        <div class="footer">
            <p>Toto je automaticky generovaný email. Neodpovedajte na túto správu.</p>
            <p>© {{ date('Y') }} Erotikon.sk - Všetky práva vyhradené</p>
            <p>
                <a href="{{ route('home') }}" style="color: #e91e63;">Navštíviť stránku</a> | 
                <a href="{{ route('support.index') }}" style="color: #e91e63;">Podpora</a> |
                <a href="{{ route('profile.edit') }}" style="color: #e91e63;">Profil</a>
            </p>
        </div>
    </div>
</body>
</html> 