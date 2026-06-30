<!DOCTYPE html>
<html>
<head>
    <title>Platby za inzeráty</title>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .stats { display: flex; gap: 20px; margin-bottom: 20px; }
        .stat-box { background: #f9f9f9; padding: 15px; border-radius: 5px; }
        .btn { padding: 5px 10px; margin: 2px; border: none; border-radius: 3px; cursor: pointer; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <h1>Platby za inzeráty</h1>
    
    <div class="stats">
        <div class="stat-box">
            <strong>Celkom platieb:</strong> {{ $stats['total'] }}
        </div>
        <div class="stat-box">
            <strong>Čakajúce:</strong> {{ $stats['pending'] }}
        </div>
        <div class="stat-box">
            <strong>Dokončené:</strong> {{ $stats['completed'] }}
        </div>
        <div class="stat-box">
            <strong>Neúspešné:</strong> {{ $stats['failed'] }}
        </div>
        <div class="stat-box">
            <strong>Bankové prevody:</strong> {{ $stats['pending_bank_transfers'] }}
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Payment ID</th>
                <th>Používateľ</th>
                <th>Inzerát</th>
                <th>Balíček</th>
                <th>Suma</th>
                <th>Metóda</th>
                <th>Stav</th>
                <th>Dátum</th>
                <th>Akcie</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->id }}</td>
                    <td>{{ $payment->payment_id }}</td>
                    <td>
                        {{ $payment->user->name }}<br>
                        <small>{{ $payment->user->email }}</small>
                    </td>
                    <td>
                        @if($payment->ad)
                            {{ $payment->ad->nickname ?: 'Inzerát #' . $payment->ad->id }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($payment->paymentPackage)
                            {{ $payment->paymentPackage->name }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $payment->amount }} {{ $payment->currency }}</td>
                    <td>{{ $payment->payment_method }}</td>
                    <td>
                        <span style="
                            @if($payment->status === 'completed') background: #d4edda; color: #155724;
                            @elseif($payment->status === 'pending') background: #fff3cd; color: #856404;
                            @elseif($payment->status === 'failed') background: #f8d7da; color: #721c24;
                            @else background: #e2e3e5; color: #383d41;
                            @endif
                            padding: 2px 8px; border-radius: 3px; font-size: 12px;">
                            {{ $payment->status }}
                        </span>
                    </td>
                    <td>{{ $payment->created_at->format('d.m.Y H:i') }}</td>
                    <td>
                        @if($payment->status === 'pending')
                            <button class="btn btn-success" onclick="approvePayment({{ $payment->id }})">
                                Potvrdiť
                            </button>
                            <button class="btn btn-danger" onclick="rejectPayment({{ $payment->id }})">
                                Zamietnuť
                            </button>
                        @else
                            <small>Spracované</small>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 20px;">
                        Žiadne platby neboli nájdené.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    @if($payments->hasPages())
        <div style="margin-top: 20px;">
            {{ $payments->links() }}
        </div>
    @endif

    <script>
    function approvePayment(paymentId) {
        if (confirm('Ste si istí, že chcete potvrdiť túto platbu?')) {
            fetch(`/admin/ads/payments/${paymentId}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Platba bola úspešne potvrdená!');
                    location.reload();
                } else {
                    alert('Chyba: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Nastala chyba pri potvrdzovaní platby.');
            });
        }
    }

    function rejectPayment(paymentId) {
        if (confirm('Ste si istí, že chcete zamietnuť túto platbu?')) {
            fetch(`/admin/ads/payments/${paymentId}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Platba bola zamietnutá!');
                    location.reload();
                } else {
                    alert('Chyba: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Nastala chyba pri zamietaní platby.');
            });
        }
    }
    </script>
</body>
</html>
