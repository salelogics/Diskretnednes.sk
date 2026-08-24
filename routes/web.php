<?php

use Illuminate\Support\Facades\File;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomerReportController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\Admin\EroticClubController;
use App\Http\Controllers\AdsController;
use App\Http\Controllers\FavoriteController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\AdminSeoController;
use App\Http\Controllers\PrivateFileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [App\Http\Controllers\PublicAdsController::class, 'index'])->name('home');
// Private profile photos serving (authenticated)
Route::middleware(['auth'])->group(function () {
    Route::get('/storage-private/{path}', [PrivateFileController::class, 'profilePhoto'])
        ->where('path', '.*')
        ->name('profile-photos.private');
});

Route::get('/eroticke-kluby', [App\Http\Controllers\EroticClubController::class, 'index'])->name('erotic-clubs');
Route::get('/eroticke-kluby/{slug}', [App\Http\Controllers\EroticClubController::class, 'show'])->name('erotic-clubs.show');

Route::get('/tantra-masaze', [App\Http\Controllers\PublicAdsController::class, 'tantra'])->name('tantra');

// Detail inzerátu (= profil) - URI zmenené z /inzerat/ na /profil/, route
// names ostávajú rovnaké, takže všetky existujúce route('ad.show', ...)
// volania v šablónach automaticky generujú novú URL bez ďalších úprav.
Route::get('/profil/{id}', [App\Http\Controllers\PublicAdsController::class, 'show'])->name('ad.show');
Route::post('/profil/{id}/klik', [App\Http\Controllers\PublicAdsController::class, 'incrementClick'])->name('ad.click');
Route::post('/profil/{id}/nahlas', [App\Http\Controllers\PublicAdsController::class, 'report'])->name('ad.report');

// Zachovaj staré /inzerat/{id} odkazy funkčné (SEO, už zdieľané linky) -
// presmerovanie na novú /profil/{id} URL.
Route::get('/inzerat/{id}', function ($id) {
    return redirect()->route('ad.show', $id, 301);
});

// Payment webhooks (outside auth middleware)
Route::post('/webhook/platby', [App\Http\Controllers\AdPaymentController::class, 'webhook'])->name('payment.webhook');


// Obľúbené inzeráty
Route::get('/oblubene', [FavoriteController::class, 'index'])->name('favorites.index');
Route::get('/oblubene/count', [FavoriteController::class, 'count'])->name('favorites.count');
Route::post('/oblubene/{ad}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
Route::get('/oblubene/{ad}/check', [FavoriteController::class, 'check'])->name('favorites.check');

Route::get('/cennik', function () {
    return view('pages.pricing');
})->name('pricing');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/kontakt', [ContactController::class, 'index'])->name('contact');
Route::post('/kontakt', [ContactController::class, 'submit'])->name('contact.submit');

Route::get('/podmienky', function () {
    return view('pages.terms');
})->name('terms');

Route::get('/sukromie', function () {
    return view('pages.privacy');
})->name('privacy');

// API endpoint pre package-id (GET endpoint bez CSRF)
Route::get('/api/package-id/{type}/{duration}', [App\Http\Controllers\AdPaymentController::class, 'getPackageId'])
    ->name('ads.payment.package-id');

// Stripe webhook (dočasne deaktivované)
// Route::post('/stripe/webhook', [App\Http\Controllers\StripeController::class, 'webhook'])->name('stripe.webhook');

// User dashboard routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/nastenka', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/ukoncit-prepnutie', [\App\Http\Controllers\Admin\AdminUsersController::class, 'stopImpersonating'])->name('stop-impersonating');
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/rozsirene', [ProfileController::class, 'updateExtended'])->name('profile.update.extended');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Support routes
    Route::get('/podpora', [App\Http\Controllers\SupportController::class, 'index'])->name('support.index');
    Route::post('/podpora', [App\Http\Controllers\SupportController::class, 'submit'])->name('support.submit');
    Route::get('/podpora/ticket/{ticket}', [App\Http\Controllers\SupportController::class, 'show'])->name('support.ticket.show');
    Route::post('/podpora/ticket/{ticket}/odpoved', [App\Http\Controllers\SupportController::class, 'reply'])->name('support.ticket.reply');
    
    // Pricing routes
    Route::get('/cennik-dashboard', [App\Http\Controllers\PricingController::class, 'index'])->name('pricing-dashboard');
    
    // Customer report routes
    Route::get('/nahlasenie-zakaznika', [App\Http\Controllers\CustomerReportController::class, 'index'])->name('customer-report.index');
    Route::post('/nahlasenie-zakaznika/vyhladat', [App\Http\Controllers\CustomerReportController::class, 'search'])->name('customer-report.search');
    Route::post('/nahlasenie-zakaznika/nahlas', [App\Http\Controllers\CustomerReportController::class, 'report'])->name('customer-report.report');
    
    // Statistics routes
    Route::get('/statistiky', [StatisticsController::class, 'index'])->name('statistics.index');
    
    // Payments routes
    Route::get('/platby', [PaymentsController::class, 'index'])->name('payments.index');
    Route::get('/platby/faktura/{payment_id}', [PaymentsController::class, 'downloadInvoice'])->name('payments.download-invoice');
    Route::post('/platby/obnovit', [PaymentsController::class, 'renewSubscription'])->name('payments.renew');
    
    // Notifications routes
    Route::get('/notifikacie', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifikacie/{id}/precitat', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifikacie/oznacit-vsetky', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifikacie/{id}', [App\Http\Controllers\NotificationController::class, 'delete'])->name('notifications.delete');
    Route::delete('/notifikacie/vymazat-precitane', [App\Http\Controllers\NotificationController::class, 'deleteRead'])->name('notifications.delete-read');
    
    // API routes for notifications
    Route::get('/api/notifikacie', [App\Http\Controllers\NotificationController::class, 'getNotifications']);
    Route::get('/api/notifikacie/pocet', [App\Http\Controllers\NotificationController::class, 'getUnreadCount']);
    
    // Ads routes
    Route::get('/inzeraty', [AdsController::class, 'index'])->name('ads.index');
    Route::get('/inzeraty/vytvorit', [AdsController::class, 'create'])->name('ads.create');
    Route::post('/inzeraty', [AdsController::class, 'store'])->name('ads.store');
    Route::get('/inzeraty/{id}/upravit', [AdsController::class, 'edit'])->name('ads.edit');
    Route::put('/inzeraty/{id}', [AdsController::class, 'update'])->name('ads.update');
    Route::post('/inzeraty/{id}/prepnut-stav', [AdsController::class, 'toggleStatus'])->name('ads.toggle-status');
    Route::post('/inzeraty/{id}/obnovit-predplatne', [AdsController::class, 'renewSubscription'])->name('ads.renew-subscription');
    Route::delete('/inzeraty/{id}', [AdsController::class, 'destroy'])->name('ads.destroy');
    Route::get('/inzeraty/{id}/statistiky', [AdsController::class, 'statistics'])->name('ads.statistics');
    Route::delete('/inzeraty/{id}/fotka/{index}', [AdsController::class, 'deleteGalleryPhoto'])->name('ads.delete-gallery-photo');

});

// Public API routes (bez autentifikácie)
Route::get('/api/ads/{ad}/status', [App\Http\Controllers\AdPaymentController::class, 'getAdStatus'])->name('api.ads.status');

// Musí byť registrovaná pred catch-all "/inzeraty/{any?}" nižšie, inak ju ten
// pohltí a presmeruje späť na "Moje inzeráty" - presne preto tlačidlo
// "Predplatiť Premium" pôsobilo, akoby sa nič nestalo. Zostáva mimo auth
// middleware (viď dôvod pri ostatných platobných routes nižšie).
Route::get('/inzeraty/{id}/predplatit', [App\Http\Controllers\AdPaymentController::class, 'showPackages'])->name('ads.payment.packages');

// Legacy redirects for old ads URLs (prevent 405 on /inzeraty/{something})
Route::get('/inzeraty/{id}', function($id) {
    // If numeric, redirect to current ad detail URL
    if (is_numeric($id)) {
        return redirect()->route('ad.show', $id);
    }
    // Otherwise redirect to ads index
    return redirect()->route('ads.index');
})->where('id', '[^/]+');

// Catch-all for any other old /inzeraty/* GET paths
Route::get('/inzeraty/{any?}', function() {
    return redirect()->route('ads.index');
})->where('any', '.*');

// DEBUG routes (len pre development)
Route::get('/debug/test-sms', [App\Http\Controllers\DebugController::class, 'testSms']);
Route::post('/debug/test-sms', [App\Http\Controllers\DebugController::class, 'testSmsSubmit']);

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/nastenka', [AdminController::class, 'dashboard'])->name('nastenka');
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/extended', [AdminController::class, 'updateExtendedProfile'])->name('profile.update.extended');
    Route::put('/profile/password', [AdminController::class, 'updatePassword'])->name('password.update');
    
    // Kluby
    Route::get('/kluby', [\App\Http\Controllers\Admin\EroticClubController::class, 'index'])->name('kluby.index');
    Route::resource('kluby', \App\Http\Controllers\Admin\EroticClubController::class)->except(['index'])->parameters(['kluby' => 'club']);
    
    // Používatelia
    Route::get('/pouzivatelia', [\App\Http\Controllers\Admin\AdminUsersController::class, 'index'])->name('pouzivatelia.index');
    Route::get('/pouzivatelia/vytvorit', [\App\Http\Controllers\Admin\AdminUsersController::class, 'create'])->name('pouzivatelia.create');
    Route::post('/pouzivatelia', [\App\Http\Controllers\Admin\AdminUsersController::class, 'store'])->name('pouzivatelia.store');
    Route::get('/pouzivatelia/{user}', [\App\Http\Controllers\Admin\AdminUsersController::class, 'show'])->name('pouzivatelia.show');
    Route::get('/pouzivatelia/{user}/upravit', [\App\Http\Controllers\Admin\AdminUsersController::class, 'edit'])->name('pouzivatelia.edit');
    Route::put('/pouzivatelia/{user}', [\App\Http\Controllers\Admin\AdminUsersController::class, 'update'])->name('pouzivatelia.update');
    Route::delete('/pouzivatelia/{user}', [\App\Http\Controllers\Admin\AdminUsersController::class, 'destroy'])->name('pouzivatelia.destroy');
    Route::post('/pouzivatelia/{user}/prepnut-sa', [\App\Http\Controllers\Admin\AdminUsersController::class, 'impersonate'])->name('pouzivatelia.impersonate');
    
    // Inzeráty
    Route::get('/inzeraty', [\App\Http\Controllers\Admin\AdminAdsController::class, 'index'])->name('inzeraty.index');
    Route::post('/inzeraty/vyhladat', [\App\Http\Controllers\Admin\AdminAdsController::class, 'search'])->name('inzeraty.search');
    Route::get('/inzeraty/vytvorit', [\App\Http\Controllers\Admin\AdminAdsController::class, 'create'])->name('inzeraty.create');
    Route::post('/inzeraty', [\App\Http\Controllers\Admin\AdminAdsController::class, 'store'])->name('inzeraty.store');
    Route::get('/inzeraty/{ad}', [\App\Http\Controllers\Admin\AdminAdsController::class, 'show'])->name('inzeraty.show');
    Route::patch('/inzeraty/{ad}/status', [\App\Http\Controllers\Admin\AdminAdsController::class, 'updateStatus'])->name('inzeraty.update-status');
    Route::post('/inzeraty/{ad}/predlzit', [\App\Http\Controllers\Admin\AdminAdsController::class, 'extendAd'])->name('inzeraty.extend');
    Route::delete('/inzeraty/{ad}', [\App\Http\Controllers\Admin\AdminAdsController::class, 'destroy'])->name('inzeraty.destroy');
    

    

    Route::post('/ads/payments/{payment}/approve', [\App\Http\Controllers\Admin\AdminAdsController::class, 'approvePayment'])->name('ads.payments.approve');
    Route::post('/ads/payments/{payment}/reject', [\App\Http\Controllers\Admin\AdminAdsController::class, 'rejectPayment'])->name('ads.payments.reject');
    Route::post('/ads/{ad}/subscribe-free', [\App\Http\Controllers\Admin\AdminAdsController::class, 'subscribeForFree'])->name('ads.subscribe-free');
    
    // API routes pre admin
    Route::get('/api/ads/{ad}/packages', [App\Http\Controllers\AdPaymentController::class, 'getPackages'])->name('api.ads.packages');
    
    // Admin predplatenie inzerátov
    
    Route::post('/inzeraty/{ad}/predplatit-platbou', [App\Http\Controllers\AdPaymentController::class, 'createPayment'])->name('inzeraty.subscribe-payment');
    
    // Články/Blog
    Route::get('/clanky', [\App\Http\Controllers\Admin\AdminBlogController::class, 'index'])->name('clanky.index');
    Route::resource('clanky', \App\Http\Controllers\Admin\AdminBlogController::class)->except(['index'])->parameters(['clanky' => 'article']);
    
    // Blog image upload routes for Editor.js
    Route::post('/clanky/upload-image', [\App\Http\Controllers\Admin\AdminBlogController::class, 'uploadImage'])->name('clanky.upload-image');
    Route::post('/clanky/upload-image-by-url', [\App\Http\Controllers\Admin\AdminBlogController::class, 'uploadImageByUrl'])->name('clanky.upload-image-by-url');
    Route::post('/clanky/fetch-url', [\App\Http\Controllers\Admin\AdminBlogController::class, 'fetchUrl'])->name('clanky.fetch-url');
    
    // Platby
    Route::get('/platby', [\App\Http\Controllers\Admin\AdminPaymentsController::class, 'index'])->name('platby.index');
    Route::resource('platby', \App\Http\Controllers\Admin\AdminPaymentsController::class)->except(['index']);
    
    // Faktúry operácie (integrované do platieb)
    Route::prefix('platby')->name('platby.')->group(function () {
        Route::get('/faktury/{platby}/stiahnut', [\App\Http\Controllers\Admin\AdminPaymentsController::class, 'downloadInvoice'])->name('invoice.download');
        Route::post('/faktury/{invoice}/pregenerovat', [\App\Http\Controllers\Admin\AdminPaymentsController::class, 'regenerateInvoice'])->name('invoice.regenerate');
        Route::post('/faktury/{invoice}/poslat-email', [\App\Http\Controllers\Admin\AdminPaymentsController::class, 'sendInvoice'])->name('invoice.send-email');
        Route::post('/faktury/{payment}/schvalit', [\App\Http\Controllers\Admin\AdminPaymentsController::class, 'approvePayment'])->name('payment.approve');
        Route::post('/faktury/{payment}/zamietnut', [\App\Http\Controllers\Admin\AdminPaymentsController::class, 'rejectPayment'])->name('payment.reject');
    });
    
    // Štatistiky
    Route::get('/statistiky', [AdminController::class, 'statistiky'])->name('statistiky.index');
    Route::get('/statistiky/data', [\App\Http\Controllers\Admin\AdminStatisticsController::class, 'getData'])->name('statistiky.data');
    Route::get('/statistiky/analytics', [\App\Http\Controllers\Admin\AdminStatisticsController::class, 'analytics'])->name('statistiky.analytics');
    
    // Nahlásenia zákazníkov
    Route::get('/nahlasenia-zakaznikov', [\App\Http\Controllers\Admin\AdminCustomerReportsController::class, 'index'])->name('nahlasenia-zakaznikov.index');
    Route::resource('nahlasenia-zakaznikov', \App\Http\Controllers\Admin\AdminCustomerReportsController::class)->except(['index']);
    
    // Nahlásenia inzerátov
    Route::resource('nahlasenia-inzeratov', \App\Http\Controllers\Admin\AdminAdReportsController::class);
    
    // Support tickets
    Route::get('/support-tickets', [SupportTicketController::class, 'adminIndex'])->name('support-tickets.index');
    Route::get('/support-tickets/{ticket}', [SupportTicketController::class, 'adminShow'])->name('support-tickets.show');
    Route::post('/support-tickets/{ticket}/respond', [SupportTicketController::class, 'respond'])->name('support-tickets.respond');
    Route::patch('/support-tickets/{ticket}/status', [SupportTicketController::class, 'updateStatus'])->name('support-tickets.update-status');
    
    // Nastavenia
    Route::get('/nastavenia', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/nastavenia', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/nastavenia/test-email', [SettingsController::class, 'testEmail'])->name('settings.test-email');
    Route::post('/nastavenia/test-admin-notifications', [SettingsController::class, 'testAdminNotifications'])->name('settings.test-admin-notifications');
    Route::post('/nastavenia/diagnose-smtp', [SettingsController::class, 'diagnoseSMTP'])->name('settings.diagnose-smtp');
    Route::post('/nastavenia/test-external-email', [SettingsController::class, 'testExternalEmail'])->name('settings.test-external-email');

    
    // Admin Notifikácie
    Route::get('/notifikacie', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifikacie/{id}/precitat', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifikacie/oznacit-vsetky', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifikacie/{id}', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'delete'])->name('notifications.delete');
    Route::delete('/notifikacie/vymazat-precitane', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'deleteRead'])->name('notifications.delete-read');
    
    // API routes pre admin notifikácie
    Route::get('/api/notifikacie', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'getNotifications']);
    Route::get('/api/notifikacie/pocet', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'getUnreadCount']);
    
    // Email Templates management
    Route::get('/email-templates', [App\Http\Controllers\Admin\AdminEmailTemplateController::class, 'index'])->name('email-templates.index');
    Route::get('/email-templates/{emailTemplate}', [App\Http\Controllers\Admin\AdminEmailTemplateController::class, 'show'])->name('email-templates.show');
    Route::get('/email-templates/{emailTemplate}/edit', [App\Http\Controllers\Admin\AdminEmailTemplateController::class, 'edit'])->name('email-templates.edit');
    Route::put('/email-templates/{emailTemplate}', [App\Http\Controllers\Admin\AdminEmailTemplateController::class, 'update'])->name('email-templates.update');
    Route::get('/email-templates/{emailTemplate}/preview', [App\Http\Controllers\Admin\AdminEmailTemplateController::class, 'preview'])->name('email-templates.preview');
    Route::get('/email-templates/{emailTemplate}/preview-raw', [App\Http\Controllers\Admin\AdminEmailTemplateController::class, 'previewRaw'])->name('email-templates.preview-raw');
    Route::post('/email-templates/{emailTemplate}/test', [App\Http\Controllers\Admin\AdminEmailTemplateController::class, 'test'])->name('email-templates.test');
    Route::post('/email-templates/{emailTemplate}/duplicate', [App\Http\Controllers\Admin\AdminEmailTemplateController::class, 'duplicate'])->name('email-templates.duplicate');
        
    // Email Log management
    Route::get('/email-log', [App\Http\Controllers\Admin\AdminEmailLogController::class, 'index'])->name('email-log.index');
    Route::get('/email-log/{emailLog}', [App\Http\Controllers\Admin\AdminEmailLogController::class, 'show'])->name('email-log.show');
    Route::delete('/email-log/{emailLog}', [App\Http\Controllers\Admin\AdminEmailLogController::class, 'destroy'])->name('email-log.destroy');
    Route::post('/email-log/bulk-delete', [App\Http\Controllers\Admin\AdminEmailLogController::class, 'bulkDelete'])->name('email-log.bulk-delete');
    Route::get('/email-log-export', [App\Http\Controllers\Admin\AdminEmailLogController::class, 'export'])->name('email-log.export');
    Route::post('/email-log/delete-all', [App\Http\Controllers\Admin\AdminEmailLogController::class, 'deleteAll'])->name('email-log.delete-all');
    
    // SEO Management
    Route::get('/seo', [App\Http\Controllers\Admin\AdminSeoController::class, 'index'])->name('seo.index');
    Route::put('/seo', [App\Http\Controllers\Admin\AdminSeoController::class, 'update'])->name('seo.update');
    Route::post('/seo/generate-sitemap', [App\Http\Controllers\Admin\AdminSeoController::class, 'generateSitemap'])->name('seo.generate-sitemap');
        
});

// Social Authentication Routes
Route::get('/auth/{provider}/redirect', [App\Http\Controllers\Auth\SocialAuthController::class, 'redirect'])
    ->name('social.redirect')
    ->where('provider', 'google|facebook');

Route::get('/auth/{provider}/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'callback'])
    ->name('social.callback')
    ->where('provider', 'google|facebook');

require __DIR__.'/auth.php';

// Test route mimo admin group
Route::get('/test-route-simple', function() {
    return 'SIMPLE TEST ROUTE WORKS!';
});

Route::get('/test-sms-interface', function () {
    return view('test-sms-interface');
});

// Test normalizácie telefónnych čísiel
Route::get('/test-phone-normalization', function() {
    $smsService = new \App\Services\SmsPaymentService();
    
    // Použijeme reflection pre prístup k private metóde
    $reflection = new ReflectionClass($smsService);
    $method = $reflection->getMethod('normalizePhoneNumber');
    $method->setAccessible(true);
    
    $testNumbers = [
        '421903123456',  // medzinárodný formát
        '4219151234567', // iný medzinárodný
        '0915967510',    // slovenský formát
        '0903123456'     // iný slovenský
    ];
    
    $results = [];
    foreach ($testNumbers as $number) {
        $results[$number] = $method->invoke($smsService, $number);
    }
    
    return response()->json($results);
});

// Test SMS simulation route
Route::get('/test-sms-simulation', function() {
    $smsService = new \App\Services\SmsPaymentService();
    
    // Simulujeme prijatie SMS "ERO FXO"
    $result = $smsService->processSmsReceived('421903123456', 'ERO FXO', 'TEST-' . time());
    
    return response()->json([
        'result' => $result,
        'database_check' => \App\Models\SmsVerification::latest()->first()
    ]);
});

// ===== AD PAYMENT ROUTES (bez auth middleware) =====
// Tieto routes musia byť mimo auth middleware, pretože platby môžu robiť aj neprihlásení užívatelia

// Ad payment routes
// ads.payment.packages (GET /inzeraty/{id}/predplatit) is registered earlier,
// before the /inzeraty/{any?} catch-all - see that definition for why.
Route::post('/inzeraty/{id}/platba', [App\Http\Controllers\AdPaymentController::class, 'createPayment'])->name('ads.payment.create');
Route::get('/platba/{paymentId}/stav', [App\Http\Controllers\AdPaymentController::class, 'showPaymentStatus'])->name('ads.payment.status');
Route::post('/platba/{paymentId}/uspech', [App\Http\Controllers\AdPaymentController::class, 'paymentSuccess'])->name('ads.payment.success');
Route::post('/platba/{paymentId}/neuspech', [App\Http\Controllers\AdPaymentController::class, 'paymentFailed'])->name('ads.payment.failed');
Route::post('/platba/{paymentId}/zrusit', [App\Http\Controllers\AdPaymentController::class, 'cancelPayment'])->name('ads.payment.cancel');
Route::get('/platba/{paymentId}/faktura', [App\Http\Controllers\AdPaymentController::class, 'downloadInvoice'])->name('ads.payment.invoice');
Route::get('/platba/{paymentId}/qr-kod', [App\Http\Controllers\AdPaymentController::class, 'getQRCode'])->name('ads.payment.qr-code');
Route::get('/platba/{paymentId}/qr-kod/stiahnut', [App\Http\Controllers\AdPaymentController::class, 'downloadQRCode'])->name('ads.payment.qr-download');
Route::get('/platba/{paymentId}/sms/navrat', [App\Http\Controllers\AdPaymentController::class, 'smsReturn'])->name('ads.payment.sms.return');

// Stripe specific routes (dočasne deaktivované)
// Route::get('/platba/{paymentId}/stripe', [App\Http\Controllers\StripeController::class, 'showPaymentForm'])->name('ads.payment.stripe.form');
// Route::post('/platba/{paymentId}/stripe/payment-intent', [App\Http\Controllers\StripeController::class, 'createPaymentIntent'])->name('ads.payment.stripe.payment-intent');
// Route::get('/platba/{paymentId}/stripe/checkout', [App\Http\Controllers\StripeController::class, 'createCheckoutSession'])->name('ads.payment.stripe.checkout');
// Route::post('/platba/{paymentId}/stripe/confirm', [App\Http\Controllers\StripeController::class, 'confirmPayment'])->name('ads.payment.stripe.confirm');
// Route::get('/platba/{paymentId}/stripe/uspech', [App\Http\Controllers\StripeController::class, 'handleSuccess'])->name('ads.payment.stripe.success');
// Route::get('/platba/{paymentId}/stripe/zrusit', [App\Http\Controllers\StripeController::class, 'handleCancel'])->name('ads.payment.stripe.cancel');
// Route::post('/platba/{paymentId}/stripe/status', [App\Http\Controllers\StripeController::class, 'checkPaymentStatus'])->name('ads.payment.stripe.status');
// Route::get('/platba/{paymentId}/stripe/simulovat-uspech', [App\Http\Controllers\StripeController::class, 'simulateSuccess'])->name('ads.payment.stripe.simulate-success');

// SMS Payment Routes - JEDNODUCHÝ SYSTÉM
Route::post('/sms/verify-code', [App\Http\Controllers\SmsPaymentController::class, 'verifyCode'])->name('sms.verify-code');
Route::match(['GET', 'POST'], '/sms/receive', [App\Http\Controllers\SmsPaymentController::class, 'receiveSms'])->name('sms.receive');
Route::match(['GET', 'POST'], '/sms/confirm', [App\Http\Controllers\SmsPaymentController::class, 'confirmPayment'])->name('sms.confirm');

// Debug routes
Route::get('/sms/test', function() {
    return response()->json(['message' => 'SMS test endpoint working']);
});
Route::post('/sms/simulate', [App\Http\Controllers\SmsPaymentController::class, 'simulateSms'])->name('sms.simulate');

// Debug routes pre SMS - zachytia akékoľvek volania
Route::any('/prijatie', function(\Illuminate\Http\Request $request) {
    \Log::info('PRIJATIE SMS CALLED', [
        'method' => $request->method(),
        'all_params' => $request->all(),
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'headers' => $request->headers->all()
    ]);
    
    // Zavolaj normálny SMS controller
    $controller = app(\App\Http\Controllers\SmsPaymentController::class);
    return $controller->receiveSms($request);
});

Route::any('/spracovanie', function(\Illuminate\Http\Request $request) {
    \Log::info('SPRACOVANIE SMS CALLED', [
        'method' => $request->method(),
        'all_params' => $request->all(),
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent()
    ]);
    
    // Zavolaj normálny SMS controller
    $controller = app(\App\Http\Controllers\SmsPaymentController::class);
    return $controller->confirmPayment($request);
});

// Catch-all pre akékoľvek SMS related volania
Route::any('/sms/{any}', function(\Illuminate\Http\Request $request, $any) {
    \Log::info('SMS CATCH-ALL CALLED', [
        'path' => $any,
        'method' => $request->method(),
        'all_params' => $request->all(),
        'ip' => $request->ip()
    ]);
    
    return response('SMS endpoint called: ' . $any, 200);
})->where('any', '.*');

// Public Sitemap Route
Route::get('/sitemap.xml', function() {
    $sitemapPath = public_path('sitemap.xml');
    
    if (!File::exists($sitemapPath)) {
        // Ak sitemap neexistuje, automaticky ju vygenerujeme
        $controller = new \App\Http\Controllers\Admin\AdminSeoController();
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('buildSitemap');
        $method->setAccessible(true);
        $sitemap = $method->invoke($controller);
        
        File::put($sitemapPath, $sitemap);
    }
    
    return response(File::get($sitemapPath))
        ->header('Content-Type', 'application/xml');
})->name('sitemap');
