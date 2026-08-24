<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Bank transfer details for Premium (topovanie) payments. Only fills a
     * row if it's still empty, so an admin who already customized a value
     * via the settings panel is left untouched.
     */
    public function up(): void
    {
        $values = [
            'invoice_company_name' => '3DIVISION s.r.o.',
            'invoice_bank_iban' => 'SK3809000000000576907983',
            'invoice_bank_swift' => 'GIBASKBXXXX',
        ];

        foreach ($values as $key => $value) {
            DB::table('settings')
                ->where('key', $key)
                ->where(function ($query) {
                    $query->whereNull('value')->orWhere('value', '');
                })
                ->update(['value' => $value]);
        }
    }

    public function down(): void
    {
        // Intentionally left in place - clearing real bank details on
        // rollback would only reintroduce the missing-payment-info bug.
    }
};
