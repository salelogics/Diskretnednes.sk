<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktúra {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            margin-bottom: 30px;
            border-bottom: 2px solid #e91e63;
            padding-bottom: 20px;
        }
        
        .header-content {
            display: table;
            width: 100%;
        }
        
        .logo-section {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .invoice-info {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: top;
        }
        
        .logo {
            max-width: 150px;
            max-height: 80px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #e91e63;
            margin-bottom: 5px;
        }
        
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #e91e63;
            margin-bottom: 10px;
        }
        
        .invoice-number {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .addresses {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .supplier, .customer {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 15px;
        }
        
        .supplier {
            background: #f8f9fa;
            border-left: 4px solid #e91e63;
        }
        
        .customer {
            background: #fff;
            border-left: 4px solid #6c757d;
        }
        
        .address-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            color: #495057;
        }
        
        .address-line {
            margin-bottom: 3px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .items-table th {
            background: #e91e63;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
        }
        
        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .items-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .totals {
            width: 300px;
            margin-left: auto;
            margin-bottom: 30px;
        }
        
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .totals td {
            padding: 8px 12px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .totals .total-row {
            background: #e91e63;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
        
        .payment-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .payment-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            color: #495057;
        }
        
        .payment-details {
            display: table;
            width: 100%;
        }
        
        .payment-left, .payment-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .footer {
            text-align: center;
            color: #6c757d;
            font-size: 11px;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }
        
        .notes {
            margin-bottom: 20px;
            padding: 15px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="logo-section">
                    @if(isset($logo) && $logo)
                        <img src="{{ $logo }}" alt="Logo" class="logo">
                    @else
                        <div class="company-name">{{ $company['name'] }}</div>
                    @endif
                </div>
                <div class="invoice-info">
                    <div class="invoice-title">FAKTÚRA</div>
                    <div class="invoice-number">Číslo: {{ $invoice->invoice_number }}</div>
                    <div>Dátum vystavenia: {{ $invoice->issue_date ? $invoice->issue_date->format('d.m.Y') : 'N/A' }}</div>
                    <div>Dátum splatnosti: {{ $invoice->due_date ? $invoice->due_date->format('d.m.Y') : 'N/A' }}</div>
                </div>
            </div>
        </div>

        <!-- Addresses -->
        <div class="addresses">
            <div class="supplier">
                <div class="address-title">DODÁVATEĽ</div>
                <div class="address-line"><strong>{{ $company['name'] }}</strong></div>
                <div class="address-line">{{ $company['address'] }}</div>
                <div class="address-line">{{ $company['postal_code'] }} {{ $company['city'] }}</div>
                <div class="address-line">{{ $company['country'] }}</div>
                <br>
                @if($company['ico'])
                    <div class="address-line">IČO: {{ $company['ico'] }}</div>
                @endif
                @if($company['dic'])
                    <div class="address-line">DIČ: {{ $company['dic'] }}</div>
                @endif
                @if($company['ic_dph'])
                    <div class="address-line">IČ DPH: {{ $company['ic_dph'] }}</div>
                @endif
                @if($company['phone'])
                    <div class="address-line">Tel: {{ $company['phone'] }}</div>
                @endif
                @if($company['email'])
                    <div class="address-line">Email: {{ $company['email'] }}</div>
                @endif
            </div>
            
            <div class="customer">
                <div class="address-title">ODBERATEĽ</div>
                <div class="address-line"><strong>{{ $invoice->customer_data['name'] ?? 'N/A' }}</strong></div>
                @if(!empty($invoice->customer_data['company']))
                    <div class="address-line">{{ $invoice->customer_data['company'] }}</div>
                @endif
                @if(!empty($invoice->customer_data['address']))
                    <div class="address-line">{{ $invoice->customer_data['address'] }}</div>
                @endif
                @if(!empty($invoice->customer_data['postal_code']) || !empty($invoice->customer_data['city']))
                    <div class="address-line">{{ $invoice->customer_data['postal_code'] }} {{ $invoice->customer_data['city'] }}</div>
                @endif
                <div class="address-line">{{ $invoice->customer_data['country'] ?? 'Slovensko' }}</div>
                @if(!empty($invoice->customer_data['phone']))
                    <div class="address-line">Tel: {{ $invoice->customer_data['phone'] }}</div>
                @endif
                @if(!empty($invoice->customer_data['email']))
                    <div class="address-line">Email: {{ $invoice->customer_data['email'] }}</div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 40%">Popis</th>
                    <th style="width: 10%" class="text-center">Množstvo</th>
                    <th style="width: 15%" class="text-right">Cena za ks</th>
                    <th style="width: 10%" class="text-center">DPH %</th>
                    <th style="width: 15%" class="text-right">DPH</th>
                    <th style="width: 15%" class="text-right">Celkom</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    @php
                        $subtotal = ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0);
                        $taxAmount = $subtotal * (($item['tax_percentage'] ?? 20) / 100);
                        $total = $subtotal + $taxAmount;
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $item['name'] ?? $item['description'] ?? 'Položka' }}</strong>
                            @if(!empty($item['description']) && $item['description'] !== ($item['name'] ?? ''))
                                <br><small style="color: #6c757d;">{{ $item['description'] }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $item['quantity'] ?? 1 }}</td>
                        <td class="text-right">€{{ number_format($item['unit_price'] ?? 0, 2, ',', ' ') }}</td>
                        <td class="text-center">{{ $item['tax_percentage'] ?? 20 }}%</td>
                        <td class="text-right">€{{ number_format($taxAmount, 2, ',', ' ') }}</td>
                        <td class="text-right">€{{ number_format($total, 2, ',', ' ') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <table>
                <tr>
                    <td>Medzisúčet:</td>
                    <td class="text-right">€{{ number_format($invoice->subtotal ?? 0, 2, ',', ' ') }}</td>
                </tr>
                <tr>
                    <td>DPH 20%:</td>
                    <td class="text-right">€{{ number_format($invoice->tax_amount ?? 0, 2, ',', ' ') }}</td>
                </tr>
                <tr class="total-row">
                    <td>CELKOM:</td>
                    <td class="text-right">€{{ number_format($invoice->total_amount ?? 0, 2, ',', ' ') }}</td>
                </tr>
            </table>
        </div>

        <!-- Payment Info -->
        @if(!empty($invoice->payment_info))
            <div class="payment-info">
                <div class="payment-title">PLATOBNÉ ÚDAJE</div>
                <div class="payment-details">
                    <div class="payment-left">
                        @if(!empty($company['iban']))
                            <div><strong>IBAN:</strong> {{ $company['iban'] }}</div>
                        @endif
                        @if(!empty($company['swift']))
                            <div><strong>SWIFT:</strong> {{ $company['swift'] }}</div>
                        @endif
                    </div>
                    <div class="payment-right">
                        @if(!empty($invoice->payment_info['variable_symbol']))
                            <div><strong>Variabilný symbol:</strong> {{ $invoice->payment_info['variable_symbol'] }}</div>
                        @endif
                        @if(!empty($invoice->payment_info['constant_symbol']))
                            <div><strong>Konštantný symbol:</strong> {{ $invoice->payment_info['constant_symbol'] }}</div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Notes -->
        @if(!empty($invoice->notes))
            <div class="notes">
                <div class="notes-title">Poznámka:</div>
                <div>{{ $invoice->notes }}</div>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Faktúra bola vygenerovaná elektronicky a je platná bez podpisu.</p>
            <p>Ďakujeme za využívanie našich služieb!</p>
        </div>
    </div>
</body>
</html> 