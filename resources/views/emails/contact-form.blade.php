<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nová správa z kontaktného formulára</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            line-height: 1.6; 
            color: #333; 
            background-color: #f4f4f4; 
            margin: 0; 
            padding: 20px; 
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            background: white; 
            border-radius: 10px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
            overflow: hidden; 
        }
        .header { 
            background: linear-gradient(135deg, #ec4899, #be185d); 
            color: white; 
            padding: 30px; 
            text-align: center; 
        }
        .header h1 { 
            margin: 0; 
            font-size: 24px; 
        }
        .content { 
            padding: 30px; 
        }
        .info-row { 
            margin: 15px 0; 
            padding: 15px; 
            background: #f8f9fa; 
            border-left: 4px solid #ec4899; 
            border-radius: 4px; 
        }
        .info-row strong { 
            color: #be185d; 
            display: block; 
            margin-bottom: 5px; 
        }
        .message-content { 
            background: #f8f9fa; 
            padding: 20px; 
            border-radius: 8px; 
            border-left: 4px solid #ec4899; 
            margin: 20px 0; 
            font-style: italic; 
        }
        .footer { 
            background: #f8f9fa; 
            padding: 20px; 
            text-align: center; 
            color: #666; 
            font-size: 12px; 
        }
        .logo { 
            font-size: 28px; 
            font-weight: bold; 
            margin-bottom: 10px; 
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">DiskretneDnes.sk</div>
            <h1>Nová správa z kontaktného formulára</h1>
        </div>
        
        <div class="content">
            <p>Dobrý deň,</p>
            <p>obdržali ste novú správu z kontaktného formulára na webovej stránke <strong>DiskretneDnes.sk</strong>.</p>
            
            <div class="info-row">
                <strong>Meno odosielateľa:</strong>
                {{ $name }}
            </div>
            
            <div class="info-row">
                <strong>Email adresa:</strong>
                <a href="mailto:{{ $email }}" style="color: #ec4899;">{{ $email }}</a>
            </div>
            
            <div class="info-row">
                <strong>Predmet správy:</strong>
                {{ $subject }}
            </div>
            
            <div class="info-row">
                <strong>Dátum a čas odoslania:</strong>
                {{ $submittedAt }}
            </div>
            
            <div class="message-content">
                <strong style="color: #be185d; font-style: normal;">Obsah správy:</strong>
                <br><br>
                {!! nl2br(e($messageContent)) !!}
            </div>
            
            <p style="margin-top: 30px;">
                <strong>Odpoveďte priamo na tento email</strong> alebo kontaktujte odosielateľa na adrese: 
                <a href="mailto:{{ $email }}" style="color: #ec4899;">{{ $email }}</a>
            </p>
        </div>
        
        <div class="footer">
            <p>Tento email bol automaticky odoslaný z kontaktného formulára na <strong>DiskretneDnes.sk</strong></p>
            <p>© {{ date('Y') }} DiskretneDnes.sk - Všetky práva vyhradené</p>
        </div>
    </div>
</body>
</html>