<?php

declare(strict_types=1);

use Elegantly\Invoices\Enums\InvoiceType;
use Elegantly\Invoices\InvoiceDiscount;
use App\Models\Invoice;
use Elegantly\Invoices\Models\InvoiceItem;

return [

    'model_invoice' => Invoice::class,
    'model_invoice_item' => InvoiceItem::class,

    'discount_class' => InvoiceDiscount::class,

    'cascade_invoice_delete_to_invoice_items' => true,

    'serial_number' => [
        /**
         * If true, will generate a serial number on creation
         * If false, you will have to set the serial_number yourself
         */
        'auto_generate' => false,

        /**
         * Define the serial number format used for each invoice type
         */
        'format' => 'YYYYCCCC',

        /**
         * Define the default prefix used for each invoice type
         */
        'prefix' => [
            InvoiceType::Invoice->value => '',
            InvoiceType::Quote->value => 'PO',
            InvoiceType::Credit->value => 'CR',
            InvoiceType::Proforma->value => 'PF',
        ],

    ],

    'date_format' => 'd.m.Y',

    'default_seller' => [
        'company' => env('COMPANY_NAME', 'Erotikon.sk'),
        'name' => env('COMPANY_CONTACT_PERSON', null),
        'address' => [
            'street' => env('COMPANY_ADDRESS', ''),
            'city' => env('COMPANY_CITY', ''),
            'postal_code' => env('COMPANY_POSTAL_CODE', ''),
            'state' => null,
            'country' => 'Slovensko',
        ],
        'email' => env('COMPANY_EMAIL', 'info@erotikon.sk'),
        'phone' => env('COMPANY_PHONE', ''),
        'tax_number' => env('COMPANY_ICO', ''),
        'fields' => [
            'DIČ' => env('COMPANY_DIC', ''),
            'IČ DPH' => env('COMPANY_IC_DPH', ''),
        ],
    ],

    /**
     * ISO 4217 currency code
     */
    'default_currency' => 'EUR',

    'pdf' => [

        'paper' => [
            'size' => 'a4',
            'orientation' => 'portrait',
        ],

        /**
         * Default DOM PDF options
         *
         * @see Available options https://github.com/barryvdh/laravel-dompdf#configuration
         */
        'options' => [
            'isRemoteEnabled' => true,
            'isPhpEnabled' => false,
            'fontHeightRatio' => 1,
            /**
             * Supported values are: 'DejaVu Sans', 'Helvetica', 'Courier', 'Times', 'Symbol', 'ZapfDingbats'
             */
            'defaultFont' => 'DejaVu Sans',

            'fontDir' => storage_path('fonts'),
            'fontCache' => storage_path('fonts'),
            'tempDir' => sys_get_temp_dir(),
            'chroot' => realpath(base_path()),
        ],

        /**
         * The logo displayed in the PDF
         */
        'logo' => public_path('images/uploads/erotikon-logo.webp'),

        /**
         * The template used to render the PDF
         */
        'template' => 'default.layout',

        'template_data' => [
            /**
             * The color displayed at the top of the PDF
             */
            'color' => '#e91e63',
        ],

    ],

];
