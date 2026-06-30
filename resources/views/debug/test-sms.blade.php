<!DOCTYPE html>
<html>
<head>
    <title>SMS Verifikácia Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        input, button { padding: 10px; margin: 10px 0; }
        input { width: 200px; }
        button { background: #007cba; color: white; border: none; cursor: pointer; }
        .result { margin-top: 20px; padding: 15px; border: 1px solid #ddd; background: #f9f9f9; }
        .success { border-color: green; background: #e8f5e8; }
        .error { border-color: red; background: #fce8e8; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 SMS Verifikácia Test</h1>
        
        <div>
            <strong>User Info:</strong><br>
            Prihlásený: {{ auth()->check() ? 'ÁNO' : 'NIE' }}<br>
            @if(auth()->check())
                User: {{ auth()->user()->name }} (ID: {{ auth()->user()->id }})<br>
                Email: {{ auth()->user()->email }}
            @endif
        </div>
        
        <hr>
        
        <form id="sms-test-form">
            <div>
                <label>Verifikačný kód:</label><br>
                <input type="text" id="verification-code" value="123456" placeholder="123456">
            </div>
            <button type="submit">🚀 Test SMS Verifikácia</button>
        </form>
        
        <div id="result"></div>
    </div>

    <script>
        document.getElementById('sms-test-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const code = document.getElementById('verification-code').value;
            const resultDiv = document.getElementById('result');
            
            resultDiv.innerHTML = '<p>⏳ Testujem...</p>';
            
            try {
                const response = await fetch('/debug/test-sms', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        verification_code: code
                    })
                });
                
                const data = await response.json();
                
                let className = response.ok ? 'result success' : 'result error';
                resultDiv.className = className;
                resultDiv.innerHTML = `
                    <h3>${response.ok ? '✅ Úspech' : '❌ Chyba'}</h3>
                    <pre>${JSON.stringify(data, null, 2)}</pre>
                `;
                
            } catch (error) {
                resultDiv.className = 'result error';
                resultDiv.innerHTML = `
                    <h3>💥 Exception</h3>
                    <p>${error.message}</p>
                `;
            }
        });
    </script>
</body>
</html> 