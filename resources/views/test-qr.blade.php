<!DOCTYPE html>
<html>
<head>
    <title>Test QR kód</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .qr-container { text-align: center; margin: 20px 0; }
        .qr-code { border: 2px solid #ccc; padding: 10px; background: white; }
        .qr-string { background: #f5f5f5; padding: 10px; margin: 10px 0; word-break: break-all; }
    </style>
</head>
<body>
    <h1>Test QR kód</h1>
    
    <div class="qr-container">
        <h2>QR kód:</h2>
        <img src="{{ $qrCodeUrl }}" alt="QR kód" class="qr-code">
    </div>
    
    <div>
        <h3>QR string:</h3>
        <div class="qr-string">{{ $qrString }}</div>
    </div>
    
    <div>
        <h3>QR URL:</h3>
        <div class="qr-string">{{ $qrCodeUrl }}</div>
    </div>
</body>
</html> 