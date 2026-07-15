<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Odpoveď na support ticket</title>
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
        .support-icon {
            font-size: 48px;
            color: #2196f3;
            margin-bottom: 20px;
        }
        .title {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .ticket-details {
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
        }
        .response-content {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
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
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-open { background-color: #ffc107; color: #212529; }
        .status-in-progress { background-color: #17a2b8; color: white; }
        .status-resolved { background-color: #28a745; color: white; }
        .status-closed { background-color: #6c757d; color: white; }
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
            <div class="support-icon">💬</div>
            <h1 class="title">Odpoveď na váš support ticket</h1>
        </div>

        <p>Dobrý deň {{ $user->name }},</p>

        <p>Dostali ste novú odpoveď na váš support ticket. Nižšie nájdete detaily:</p>

        <div class="ticket-details">
            <h3 style="margin-top: 0; color: #2196f3;">Detaily ticketu</h3>
            
            <div class="detail-row">
                <span class="detail-label">Číslo ticketu:</span>
                <span class="detail-value">#{{ $ticket->id }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Predmet:</span>
                <span class="detail-value">{{ $ticket->subject }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Stav:</span>
                <span class="detail-value">
                    <span class="status-badge status-{{ $ticket->status }}">
                        @switch($ticket->status)
                            @case('open')
                                Otvorený
                                @break
                            @case('in_progress')
                                V riešení
                                @break
                            @case('resolved')
                                Vyriešený
                                @break
                            @case('closed')
                                Uzavretý
                                @break
                            @default
                                {{ $ticket->status }}
                        @endswitch
                    </span>
                </span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Vytvorený:</span>
                <span class="detail-value">{{ $ticket->created_at->format('d.m.Y H:i') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Posledná aktualizácia:</span>
                <span class="detail-value">{{ $ticket->updated_at->format('d.m.Y H:i') }}</span>
            </div>
        </div>

        @if($ticket->admin_response)
        <div class="response-content">
            <h3 style="margin-top: 0; color: #28a745;">Odpoveď od podpory</h3>
            <p style="margin-bottom: 0;">{{ $ticket->admin_response }}</p>
        </div>
        @endif

        <p>
            @if($ticket->status === 'resolved')
                Váš ticket bol označený ako vyriešený. Ak máte ďalšie otázky alebo problém nie je vyriešený, môžete odpovedať na tento ticket.
            @elseif($ticket->status === 'closed')
                Váš ticket bol uzavretý. Ak potrebujete ďalšiu pomoc, vytvorte nový ticket.
            @else
                Ak máte ďalšie otázky k tomuto ticketu, môžete odpovedať priamo v systéme.
            @endif
        </p>

        <div style="text-align: center;">
            <a href="{{ route('support.index') }}" class="button">Zobraziť ticket</a>
        </div>

        <p>Ďakujeme za trpezlivosť a využívanie našich služieb!</p>

        <div class="footer">
            <p>Toto je automaticky generovaný email. Neodpovedajte na túto správu.</p>
            <p>© {{ date('Y') }} DiskretneDnes.sk - Všetky práva vyhradené</p>
            <p>
                <a href="{{ route('home') }}" style="color: #e91e63;">Navštíviť stránku</a> | 
                <a href="{{ route('support.index') }}" style="color: #e91e63;">Podpora</a>
            </p>
        </div>
    </div>
</body>
</html> 