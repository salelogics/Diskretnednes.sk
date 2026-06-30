<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Ad;
use App\Models\PaymentPackage;
use App\Models\AdPayment;
use App\Services\StripeService;

class StripePaymentTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $ad;
    protected $package;

    protected function setUp(): void
    {
        parent::setUp();

        // Vytvoríme test používateľa
        $this->user = User::factory()->create();

        // Vytvoríme test inzerát
        $this->ad = Ad::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'active'
        ]);

        // Vytvoríme test balíček
        $this->package = PaymentPackage::factory()->create([
            'name' => 'Test Package',
            'price' => 10.00,
            'duration_days' => 30
        ]);
    }

    public function test_user_can_access_stripe_payment_form()
    {
        // Vytvoríme platbu
        $payment = AdPayment::create([
            'user_id' => $this->user->id,
            'ad_id' => $this->ad->id,
            'payment_package_id' => $this->package->id,
            'payment_id' => 'test-payment-' . uniqid(),
            'amount' => $this->package->price,
            'currency' => 'EUR',
            'payment_method' => 'stripe',
            'status' => 'pending',
            'duration_days' => $this->package->duration_days
        ]);

        // Prihlásiť používateľa
        $this->actingAs($this->user);

        // Pristúpiť na Stripe formulár
        $response = $this->get(route('ads.payment.stripe.form', $payment->payment_id));

        $response->assertStatus(200);
        $response->assertViewIs('ads.payment.stripe');
        $response->assertViewHas('payment', $payment);
    }

    public function test_user_cannot_access_other_users_payment()
    {
        // Vytvoríme iného používateľa
        $otherUser = User::factory()->create();

        // Vytvoríme platbu pre iného používateľa
        $payment = AdPayment::create([
            'user_id' => $otherUser->id,
            'ad_id' => $this->ad->id,
            'payment_package_id' => $this->package->id,
            'payment_id' => 'test-payment-' . uniqid(),
            'amount' => $this->package->price,
            'currency' => 'EUR',
            'payment_method' => 'stripe',
            'status' => 'pending',
            'duration_days' => $this->package->duration_days
        ]);

        // Prihlásiť používateľa
        $this->actingAs($this->user);

        // Pokusiť sa pristúpiť na platbu iného používateľa
        $response = $this->get(route('ads.payment.stripe.form', $payment->payment_id));

        $response->assertStatus(404);
    }

    public function test_completed_payment_redirects_to_status_page()
    {
        // Vytvoríme dokončenú platbu
        $payment = AdPayment::create([
            'user_id' => $this->user->id,
            'ad_id' => $this->ad->id,
            'payment_package_id' => $this->package->id,
            'payment_id' => 'test-payment-' . uniqid(),
            'amount' => $this->package->price,
            'currency' => 'EUR',
            'payment_method' => 'stripe',
            'status' => 'completed',
            'duration_days' => $this->package->duration_days
        ]);

        // Prihlásiť používateľa
        $this->actingAs($this->user);

        // Pokusiť sa pristúpiť na Stripe formulár
        $response = $this->get(route('ads.payment.stripe.form', $payment->payment_id));

        $response->assertRedirect(route('ads.payment.status', $payment->payment_id));
        $response->assertSessionHas('error', 'Táto platba už bola spracovaná.');
    }

    public function test_create_payment_intent_returns_json()
    {
        // Vytvoríme platbu
        $payment = AdPayment::create([
            'user_id' => $this->user->id,
            'ad_id' => $this->ad->id,
            'payment_package_id' => $this->package->id,
            'payment_id' => 'test-payment-' . uniqid(),
            'amount' => $this->package->price,
            'currency' => 'EUR',
            'payment_method' => 'stripe',
            'status' => 'pending',
            'duration_days' => $this->package->duration_days
        ]);

        // Prihlásiť používateľa
        $this->actingAs($this->user);

        // Mock Stripe service
        $this->mock(StripeService::class, function ($mock) {
            $mock->shouldReceive('createPaymentIntent')
                ->once()
                ->andReturn([
                    'success' => true,
                    'client_secret' => 'pi_test_client_secret',
                    'payment_intent_id' => 'pi_test_123'
                ]);
        });

        // Vytvoriť Payment Intent
        $response = $this->postJson(route('ads.payment.stripe.payment-intent', $payment->payment_id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'client_secret' => 'pi_test_client_secret'
        ]);
    }

    public function test_checkout_session_redirects_to_stripe()
    {
        // Vytvoríme platbu
        $payment = AdPayment::create([
            'user_id' => $this->user->id,
            'ad_id' => $this->ad->id,
            'payment_package_id' => $this->package->id,
            'payment_id' => 'test-payment-' . uniqid(),
            'amount' => $this->package->price,
            'currency' => 'EUR',
            'payment_method' => 'stripe',
            'status' => 'pending',
            'duration_days' => $this->package->duration_days
        ]);

        // Prihlásiť používateľa
        $this->actingAs($this->user);

        // Mock Stripe service
        $this->mock(StripeService::class, function ($mock) {
            $mock->shouldReceive('createCheckoutSession')
                ->once()
                ->andReturn([
                    'success' => true,
                    'checkout_url' => 'https://checkout.stripe.com/pay/test_session'
                ]);
        });

        // Vytvoriť Checkout Session
        $response = $this->get(route('ads.payment.stripe.checkout', $payment->payment_id));

        $response->assertRedirect('https://checkout.stripe.com/pay/test_session');
    }

    public function test_simulate_success_marks_payment_as_completed()
    {
        // Nastaviť environment na local
        config(['app.env' => 'local']);

        // Vytvoríme platbu
        $payment = AdPayment::create([
            'user_id' => $this->user->id,
            'ad_id' => $this->ad->id,
            'payment_package_id' => $this->package->id,
            'payment_id' => 'test-payment-' . uniqid(),
            'amount' => $this->package->price,
            'currency' => 'EUR',
            'payment_method' => 'stripe',
            'status' => 'pending',
            'duration_days' => $this->package->duration_days
        ]);

        // Prihlásiť používateľa
        $this->actingAs($this->user);

        // Simulovať úspešnú platbu
        $response = $this->get(route('ads.payment.stripe.simulate-success', $payment->payment_id));

        // Overiť presmerovanie
        $response->assertRedirect(route('ads.payment.status', $payment->payment_id));
        $response->assertSessionHas('success', 'Platba bola simulovaná ako úspešná!');

        // Overiť, že platba bola označená ako dokončená
        $payment->refresh();
        $this->assertEquals('completed', $payment->status);
        $this->assertNotNull($payment->subscription_starts_at);
        $this->assertNotNull($payment->subscription_ends_at);
    }

    public function test_simulate_success_not_available_in_production()
    {
        // Nastaviť environment na production
        config(['app.env' => 'production']);

        // Vytvoríme platbu
        $payment = AdPayment::create([
            'user_id' => $this->user->id,
            'ad_id' => $this->ad->id,
            'payment_package_id' => $this->package->id,
            'payment_id' => 'test-payment-' . uniqid(),
            'amount' => $this->package->price,
            'currency' => 'EUR',
            'payment_method' => 'stripe',
            'status' => 'pending',
            'duration_days' => $this->package->duration_days
        ]);

        // Prihlásiť používateľa
        $this->actingAs($this->user);

        // Pokusiť sa simulovať úspešnú platbu
        $response = $this->get(route('ads.payment.stripe.simulate-success', $payment->payment_id));

        $response->assertStatus(404);
    }
}
