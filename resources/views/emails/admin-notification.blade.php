<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin notifikácia</title>
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
        .admin-badge { display: none; }
        /* Ikonu priority odstraňujeme, aby dizajn sedel s ostatnými emailami */
        
        .title {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .notification-content {
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid;
        }
        .type-payment { 
            background-color: #d1ecf1; 
            border-left-color: #17a2b8; 
        }
        .type-support { 
            background-color: #cce5ff; 
            border-left-color: #007bff; 
        }
        .type-moderation { 
            background-color: #f8d7da; 
            border-left-color: #dc3545; 
        }
        .type-system { 
            background-color: #e2e3e5; 
            border-left-color: #6c757d; 
        }
        .type-ad { 
            background-color: #e7e3ff; 
            border-left-color: #6f42c1; 
        }
        .priority-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 15px;
        }
        .priority-urgent { background-color: #dc3545; color: white; }
        .priority-high { background-color: #fd7e14; color: white; }
        .priority-normal { background-color: #0d6efd; color: white; }
        .priority-low { background-color: #6c757d; color: white; }
        
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
            <h1 class="title">{{ $title }}</h1>
        </div>

        <div class="notification-content type-{{ $type }}">
            @if($priority !== 'normal')
            <div class="priority-badge priority-{{ $priority }}">
                @switch($priority)
                    @case('urgent')
                        URGENTNÉ
                        @break
                    @case('high')
                        VYSOKÁ PRIORITA
                        @break
                    @case('low')
                        NÍZKA PRIORITA
                        @break
                    @default
                        {{ strtoupper($priority) }}
                @endswitch
            </div>
            @endif
            
                            <p style="margin-bottom: 0; font-size: 16px;">{{ $content }}</p>
        </div>

        @if($actionUrl && $actionText)
        <div style="text-align: center;">
            <a href="{{ $actionUrl }}" class="button">{{ $actionText }}</a>
        </div>
        @endif

        

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