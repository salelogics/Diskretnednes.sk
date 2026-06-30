<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMTP Test z Erotikon.sk</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .success-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
        .detail-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .info-table th, .info-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .info-table th {
            background-color: #f1f1f1;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="success-icon">✅</div>
        <h1>SMTP Test Úspešný!</h1>
        <p>Email z Erotikon.sk bol úspešne doručený</p>
    </div>

    <div class="content">
        <div class="detail-box">
            <h2>🎉 Gratulujem!</h2>
            <p>Ak čítate túto správu, znamená to, že <strong>SMTP konfigurácia funguje správne</strong> a emaily sa úspešne doručujú z vašej aplikácie.</p>
        </div>

        <h3>📋 Detaily testu:</h3>
        <table class="info-table">
            <tr>
                <th>Čas odoslania</th>
                <td>{{ $testTime }}</td>
            </tr>
            <tr>
                <th>Odosielateľ</th>
                <td>{{ $fromAddress }}</td>
            </tr>
            <tr>
                <th>SMTP Server</th>
                <td>{{ $smtpHost }}</td>
            </tr>
            <tr>
                <th>Port</th>
                <td>{{ $smtpPort }}</td>
            </tr>
            <tr>
                <th>Šifrovanie</th>
                <td>{{ strtoupper($encryption) }}</td>
            </tr>
        </table>

        <div class="detail-box">
            <h3>✅ Čo to znamená?</h3>
            <ul>
                <li>✅ SMTP server je dostupný a funguje</li>
                <li>✅ Autentifikácia je správna</li>
                <li>✅ Email sa úspešne odoslal</li>
                <li>✅ Konfigurácia je správna</li>
            </ul>
        </div>

        <div class="detail-box">
            <h3>🔧 Ak sa emaily nedoručujú interno:</h3>
            <ul>
                <li>Skontrolujte SPAM priečinky</li>
                <li>Overijte či máte povolené odosielanie na hostingu</li>
                <li>Kontaktujte HostCreators support</li>
                <li>Skúste zmeniť port na 587 s TLS šifrovaním</li>
            </ul>
        </div>

        <div class="footer">
            <p>Tento email bol odoslaný z <strong>Erotikon.sk</strong> admin panelu</p>
            <p>Testovací email - {{ $testTime }}</p>
        </div>
    </div>
</body>
</html> 