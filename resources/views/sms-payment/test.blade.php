@extends('layouts.app')

@section('title', 'Test SMS Platieb')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8">Test SMS Platieb</h1>
        
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Online integrácia test -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Online integrácia (Presmerovanie)</h2>
            <p class="text-gray-600 mb-4">Test vytvorenia platby a presmerovania na PlatbaMobilom.sk</p>
            
            <form action="{{ route('sms.payment.create') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Popis platby</label>
                    <input type="text" name="description" value="Test platba DISKRETNEDNES" maxlength="30"
                           class="w-full border border-gray-300 rounded-md px-3 py-2" required>
                    <p class="text-xs text-gray-500">Max 30 znakov, len a-zA-Z0-9 .-</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cena (€)</label>
                    <select name="price" class="w-full border border-gray-300 rounded-md px-3 py-2" required>
                        <option value="5.00" selected>5.00 € (Classic 1 deň)</option>
                        <option value="7.00">7.00 € (Premium 1 deň / Classic 2 dni)</option>
                        <option value="9.00">9.00 € (Classic 3 dni)</option>
                        <option value="10.00">10.00 € (Gold 1 deň / Premium 2 dni / Classic 5 dní)</option>
                        <option value="13.00">13.00 € (Gold 2 dni / Premium 3 dni)</option>
                        <option value="15.00">15.00 € (Gold 3 dni / Premium 5 dní)</option>
                        <option value="20.00">20.00 € (Gold 5 dní)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Návratová URL</label>
                    <input type="url" name="return_url" value="{{ route('sms.payment.response') }}" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email (voliteľné)</label>
                    <input type="email" name="email" value="test@diskretnednes.sk" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2">
                </div>
                
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Vytvoriť test platbu
                </button>
            </form>
        </div>

        <!-- Offline integrácia info -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Offline integrácia (SMS webhooks)</h2>
            <p class="text-gray-600 mb-4">Webhooks pre prijímanie SMS od PlatbaMobilom.sk</p>
            
            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                <div>
                    <strong>SMS Receive URL:</strong>
                    <code class="bg-gray-200 px-2 py-1 rounded text-sm">{{ route('sms.receive') }}</code>
                </div>
                <div>
                    <strong>SMS Confirm URL:</strong>
                    <code class="bg-gray-200 px-2 py-1 rounded text-sm">{{ route('sms.confirm') }}</code>
                </div>
            </div>
            
            <div class="mt-4">
                <h3 class="font-semibold mb-2">Podporované SMS príkazy (kľúčové slovo: ERO):</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">1 deň topovanie:</h4>
                        <ul class="space-y-1">
                            <li><code>ERO FXO</code> - Classic (5 €)</li>
                            <li><code>ERO CC3</code> - Premium (7 €)</li>
                            <li><code>ERO EXV</code> - Gold (10 €)</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">2 dni topovanie:</h4>
                        <ul class="space-y-1">
                            <li><code>ERO 9E8</code> - Classic (7 €)</li>
                            <li><code>ERO Y7Q</code> - Premium (10 €)</li>
                            <li><code>ERO VXH</code> - Gold (13 €)</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">3 dni topovanie:</h4>
                        <ul class="space-y-1">
                            <li><code>ERO LS2</code> - Classic (9 €)</li>
                            <li><code>ERO ILC</code> - Premium (13 €)</li>
                            <li><code>ERO 0RP</code> - Gold (15 €)</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">5 dní topovanie:</h4>
                        <ul class="space-y-1">
                            <li><code>ERO 8E2</code> - Classic (10 €)</li>
                            <li><code>ERO EDG</code> - Premium (15 €)</li>
                            <li><code>ERO 7FO</code> - Gold (20 €)</li>
                        </ul>
                    </div>
                </div>
                <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="text-sm text-blue-800">
                        <strong>Poznámka:</strong> SMS platby používajú dvojstupňový verifikačný systém - po odoslaní SMS dostanete verifikačný kód, ktorý zadáte na webovej stránke.
                        <br><strong>Cenové hladiny:</strong> 5€ | 7€ | 9€ | 10€ | 13€ | 15€ | 20€
                        <br><strong>Formát:</strong> Všetky kódy sú s medzerou (napr. ERO FXO, nie EROFXO)
                    </div>
                </div>
            </div>
        </div>

        <!-- Test podpisov -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Test SMS simulácie</h2>
            <p class="text-gray-600 mb-4">Simulujte SMS správy bez skutočného odoslania</p>
            
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefónne číslo</label>
                        <input type="text" id="testMsisdn" value="421903123456" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Text SMS</label>
                        <select id="testSmsText" class="w-full border border-gray-300 rounded-md px-3 py-2">
                            <option value="ERO FXO">ERO FXO (Classic 1 deň - 5€)</option>
                            <option value="ERO CC3">ERO CC3 (Premium 1 deň - 7€)</option>
                            <option value="ERO EXV">ERO EXV (Gold 1 deň - 10€)</option>
                            <option value="ERO 9E8">ERO 9E8 (Classic 2 dni - 7€)</option>
                            <option value="ERO Y7Q">ERO Y7Q (Premium 2 dni - 10€)</option>
                            <option value="ERO VXH">ERO VXH (Gold 2 dni - 13€)</option>
                            <option value="ERO LS2">ERO LS2 (Classic 3 dni - 9€)</option>
                            <option value="ERO ILC">ERO ILC (Premium 3 dni - 13€)</option>
                            <option value="ERO 0RP">ERO 0RP (Gold 3 dni - 15€)</option>
                            <option value="ERO 8E2">ERO 8E2 (Classic 5 dní - 10€)</option>
                            <option value="ERO EDG">ERO EDG (Premium 5 dní - 15€)</option>
                            <option value="ERO 7FO">ERO 7FO (Gold 5 dní - 20€)</option>
                            <option value="ERO HELP">ERO HELP (Nápoveda)</option>
                        </select>
                    </div>
                </div>
                
                <button onclick="testSmsSimulation()" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Simulovať SMS správu
                </button>
                
                <div id="smsTestResult" class="hidden">
                    <h3 class="font-semibold mb-2">Výsledok simulácie:</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <pre id="smsTestOutput" class="text-sm text-gray-800 whitespace-pre-wrap"></pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500">
                Táto stránka je dostupná len v development prostredí.
            </p>
        </div>
    </div>
</div>

<script>
function testSmsSimulation() {
    const msisdn = document.getElementById('testMsisdn').value;
    const text = document.getElementById('testSmsText').value;
    const resultDiv = document.getElementById('smsTestResult');
    const outputPre = document.getElementById('smsTestOutput');
    
    // Zobrazíme loading
    outputPre.textContent = 'Spracovávam SMS správu...';
    resultDiv.classList.remove('hidden');
    
    // Vytvoríme URL s parametrami
    const url = new URL('{{ route("sms.simulate") }}', window.location.origin);
    url.searchParams.append('msisdn', msisdn);
    url.searchParams.append('text', text);
    url.searchParams.append('id', 'test_' + Date.now());
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            outputPre.textContent = JSON.stringify(data, null, 2);
        })
        .catch(error => {
            outputPre.textContent = 'Chyba: ' + error.message;
        });
}
</script>
@endsection 