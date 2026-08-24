<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class MissingEmailTemplatesSeeder extends Seeder
{
    public function run()
    {
        $templates = [
            // 1. Šablóna pre obnovu hesla
            [
                'key' => 'password_reset',
                'name' => 'Obnova hesla',
                'type' => 'user',
                'subject' => 'Obnova hesla - DiskretneDnes.sk',
                'content' => $this->getPasswordResetTemplate(),
                'variables' => json_encode([
                    'user_name' => 'Meno používateľa',
                    'user_email' => 'Email používateľa',
                    'reset_url' => 'Link na reset hesla',
                    'expires_at' => 'Čas expirácie linku'
                ]),
                'is_active' => true
            ],
            
            // 2. Šablóna pre admina ak sa vytvorí nová platba
            [
                'key' => 'admin_payment_created',
                'name' => 'Nová platba - admin',
                'type' => 'admin',
                'subject' => 'Nová platba vytvorená - DiskretneDnes.sk Admin',
                'content' => $this->getAdminPaymentCreatedTemplate(),
                'variables' => json_encode([
                    'user_name' => 'Meno používateľa', 
                    'user_email' => 'Email používateľa',
                    'payment_id' => 'ID platby',
                    'amount' => 'Suma platby',
                    'ad_id' => 'ID inzerátu',
                    'ad_nickname' => 'Názov inzerátu',
                    'package_type' => 'Typ balíčku',
                    'created_at' => 'Dátum vytvorenia'
                ]),
                'is_active' => true
            ],
            
            // 3. Šablóna ak je platba úspešná
            [
                'key' => 'payment_success',
                'name' => 'Platba úspešná',
                'type' => 'user',
                'subject' => 'Platba úspešne spracovaná - DiskretneDnes.sk',
                'content' => $this->getPaymentSuccessTemplate(),
                'variables' => json_encode([
                    'user_name' => 'Meno používateľa',
                    'payment_id' => 'ID platby',
                    'amount' => 'Suma platby',
                    'ad_id' => 'ID inzerátu',
                    'ad_nickname' => 'Názov inzerátu',
                    'package_type' => 'Typ balíčku',
                    'expires_at' => 'Dátum expirácie'
                ]),
                'is_active' => true
            ],
            
            // 4. Šablóna ak sa blíži koniec predplatného
            [
                'key' => 'subscription_expiring',
                'name' => 'Predplatné sa končí',
                'type' => 'user',
                'subject' => 'Vaše predplatné sa končí za {{days_left}} dní - DiskretneDnes.sk',
                'content' => $this->getSubscriptionExpiringTemplate(),
                'variables' => json_encode([
                    'user_name' => 'Meno používateľa',
                    'ad_id' => 'ID inzerátu',
                    'ad_nickname' => 'Názov inzerátu',
                    'expires_at' => 'Dátum expirácie',
                    'days_left' => 'Zostáva dní',
                    'renew_url' => 'Link na predĺženie'
                ]),
                'is_active' => true
            ],
            
            // 5. Šablóna keď sa predplatné skončilo
            [
                'key' => 'subscription_expired',
                'name' => 'Predplatné vypršalo',
                'type' => 'user',
                'subject' => 'Vaše predplatné vypršalo - DiskretneDnes.sk',
                'content' => $this->getSubscriptionExpiredTemplate(),
                'variables' => json_encode([
                    'user_name' => 'Meno používateľa',
                    'ad_id' => 'ID inzerátu',
                    'ad_nickname' => 'Názov inzerátu',
                    'expired_at' => 'Dátum expirácie',
                    'renew_url' => 'Link na obnovenie'
                ]),
                'is_active' => true
            ]
        ];

        foreach ($templates as $templateData) {
            EmailTemplate::updateOrCreate(
                ['key' => $templateData['key']],
                $templateData
            );
        }
    }

    private function getPasswordResetTemplate(): string
    {
        return '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff;">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;">
                    <h1 style="color: white; margin: 0; font-size: 28px;">🔐 Obnova hesla</h1>
                    <p style="color: #f8f9ff; margin: 10px 0 0 0; font-size: 16px;">DiskretneDnes.sk</p>
                </div>
                
                <div style="padding: 30px; background-color: #f8f9fa;">
                    <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                        <h2 style="color: #333; margin-top: 0;">Ahoj {{user_name}}!</h2>
                        
                        <p style="color: #666; line-height: 1.6; margin: 20px 0;">
                            Dostali sme požiadavku na obnovenie hesla pre váš účet <strong>{{user_email}}</strong>.
                        </p>
                        
                        <div style="margin: 30px 0; text-align: center;">
                            <a href="{{reset_url}}" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; display: inline-block; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                                🔑 Obnoviť heslo
                            </a>
                        </div>
                        
                        <div style="margin: 20px 0; padding: 15px; background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px;">
                            <p style="margin: 0; color: #856404; font-size: 14px;">
                                ⏰ <strong>Dôležité:</strong> Tento link je platný do {{expires_at}}
                            </p>
                        </div>
                        
                        <p style="color: #666; line-height: 1.6; margin: 20px 0;">
                            Ak ste nepožiadali o obnovenie hesla, jednoducho tento email ignorujte. 
                            Vaše heslo zostane nezmenené.
                        </p>
                        
                        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; text-align: center;">
                            <p style="color: #999; font-size: 12px; margin: 0;">
                                Z bezpečnostných dôvodov nekopírujte tento link. Použite tlačidlo vyššie.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div style="background-color: #667eea; padding: 20px; text-align: center;">
                    <p style="color: white; margin: 0; font-size: 14px;">© 2025 DiskretneDnes.sk</p>
                </div>
            </div>';
    }

    private function getAdminPaymentCreatedTemplate(): string
    {
        return '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff;">
                <div style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); padding: 30px; text-align: center;">
                    <h1 style="color: white; margin: 0; font-size: 28px;">💰 Nová platba</h1>
                    <p style="color: #d1ecf1; margin: 10px 0 0 0; font-size: 16px;">v systéme DiskretneDnes.sk</p>
                </div>
                
                <div style="padding: 30px; background-color: #f8fff9;">
                    <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-left: 5px solid #28a745;">
                        <h2 style="color: #333; margin-top: 0;">
                            💳 Platba #{{payment_id}}
                        </h2>
                        
                        <div style="margin: 20px 0; padding: 20px; background: linear-gradient(135deg, #d4edda 0%, #f8fff9 100%); border-radius: 8px;">
                            <h3 style="margin: 0 0 15px 0; color: #28a745;">Detaily platby</h3>
                            <p style="margin: 5px 0; color: #666;"><strong>💰 Suma:</strong> {{amount}} €</p>
                            <p style="margin: 5px 0; color: #666;"><strong>👤 Zákazník:</strong> {{user_name}} ({{user_email}})</p>
                            <p style="margin: 5px 0; color: #666;"><strong>📅 Vytvorené:</strong> {{created_at}}</p>
                        </div>
                        
                        <div style="margin: 20px 0; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                            <h3 style="margin: 0 0 15px 0; color: #6f42c1;">Inzerát</h3>
                            <p style="margin: 5px 0; color: #666;"><strong>🎯 ID:</strong> #{{ad_id}}</p>
                            <p style="margin: 5px 0; color: #666;"><strong>📝 Názov:</strong> {{ad_nickname}}</p>
                            <p style="margin: 5px 0; color: #666;"><strong>📦 Balíček:</strong> {{package_type}}</p>
                        </div>
                        
                        <div style="margin: 20px 0;">
                            <h3 style="color: #28a745; margin-bottom: 15px;">🔧 Admin akcie:</h3>
                            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                                <ul style="margin: 0; padding-left: 20px; line-height: 1.8;">
                                    <li>✅ Skontrolujte stav platby</li>
                                    <li>👁️ Overte aktiváciu služieb</li>
                                    <li>📊 Sledujte platobné štatistiky</li>
                                    <li>📧 V prípade problému kontaktujte zákazníka</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div style="margin-top: 30px; text-align: center;">
                            <div style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 15px 30px; border-radius: 25px; display: inline-block;">
                                <strong>💹 Nový príjem v systéme</strong>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div style="background-color: #28a745; padding: 20px; text-align: center;">
                    <p style="color: white; margin: 0; font-size: 14px;">© 2025 DiskretneDnes.sk - Admin Panel</p>
                </div>
            </div>';
    }

    private function getPaymentSuccessTemplate(): string
    {
        return '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff;">
                <div style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); padding: 30px; text-align: center;">
                    <h1 style="color: white; margin: 0; font-size: 28px;">✅ Platba úspešná!</h1>
                    <p style="color: #d1ecf1; margin: 10px 0 0 0; font-size: 16px;">Ďakujeme za dôveru</p>
                </div>
                
                <div style="padding: 30px; background-color: #f8fff9;">
                    <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-left: 5px solid #28a745;">
                        <h2 style="color: #333; margin-top: 0;">Ahoj {{user_name}}!</h2>
                        
                        <p style="color: #28a745; font-size: 18px; font-weight: bold; margin: 20px 0;">
                            🎉 Vaša platba bola úspešne spracovaná!
                        </p>
                        
                        <div style="margin: 20px 0; padding: 20px; background: linear-gradient(135deg, #d4edda 0%, #f8fff9 100%); border-radius: 8px;">
                            <h3 style="margin: 0 0 15px 0; color: #28a745;">Detaily platby</h3>
                            <p style="margin: 5px 0; color: #666;"><strong>💳 ID platby:</strong> {{payment_id}}</p>
                            <p style="margin: 5px 0; color: #666;"><strong>💰 Suma:</strong> {{amount}} €</p>
                            <p style="margin: 5px 0; color: #666;"><strong>📦 Balíček:</strong> {{package_type}}</p>
                        </div>
                        
                        <div style="margin: 20px 0; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                            <h3 style="margin: 0 0 15px 0; color: #6f42c1;">Váš inzerát</h3>
                            <p style="margin: 5px 0; color: #666;"><strong>🎯 ID:</strong> #{{ad_id}}</p>
                            <p style="margin: 5px 0; color: #666;"><strong>📝 Názov:</strong> {{ad_nickname}}</p>
                            <p style="margin: 5px 0; color: #666;"><strong>⏰ Aktívny do:</strong> {{expires_at}}</p>
                        </div>
                        
                        <div style="margin: 20px 0;">
                            <h3 style="color: #28a745; margin-bottom: 15px;">🚀 Čo ďalej?</h3>
                            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                                <ul style="margin: 0; padding-left: 20px; line-height: 1.8;">
                                    <li>✅ Váš inzerát je teraz aktívny</li>
                                    <li>👀 Návštevníci si ho môžu prezerať</li>
                                    <li>📊 Môžete sledovať štatistiky v profile</li>
                                    <li>📧 Pošleme vám upozornenie pred vypršaním</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div style="margin-top: 30px; text-align: center;">
                            <div style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 15px 30px; border-radius: 25px; display: inline-block;">
                                <strong>🌟 Želáme veľa úspechov!</strong>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div style="background-color: #28a745; padding: 20px; text-align: center;">
                    <p style="color: white; margin: 0; font-size: 14px;">© 2025 DiskretneDnes.sk</p>
                </div>
            </div>';
    }

    private function getSubscriptionExpiringTemplate(): string
    {
        return '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff;">
                <div style="background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%); padding: 30px; text-align: center;">
                    <h1 style="color: white; margin: 0; font-size: 28px;">⏰ Upozornenie</h1>
                    <p style="color: #fff8dc; margin: 10px 0 0 0; font-size: 16px;">Vaše predplatné sa končí</p>
                </div>
                
                <div style="padding: 30px; background-color: #fffbf0;">
                    <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-left: 5px solid #ffc107;">
                        <h2 style="color: #333; margin-top: 0;">Ahoj {{user_name}}!</h2>
                        
                        <p style="color: #ff8c00; font-size: 18px; font-weight: bold; margin: 20px 0;">
                            ⚠️ Vaše predplatné sa končí za {{days_left}} dní!
                        </p>
                        
                        <div style="margin: 20px 0; padding: 20px; background: linear-gradient(135deg, #fff3cd 0%, #fffbf0 100%); border-radius: 8px;">
                            <h3 style="margin: 0 0 15px 0; color: #ff8c00;">Detaily inzerátu</h3>
                            <p style="margin: 5px 0; color: #666;"><strong>🎯 ID:</strong> #{{ad_id}}</p>
                            <p style="margin: 5px 0; color: #666;"><strong>📝 Názov:</strong> {{ad_nickname}}</p>
                            <p style="margin: 5px 0; color: #666;"><strong>⏰ Končí:</strong> {{expires_at}}</p>
                        </div>
                        
                        <div style="margin: 20px 0; padding: 15px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px;">
                            <p style="margin: 0; color: #721c24; font-size: 14px;">
                                🚨 <strong>Pozor!</strong> Po vypršaní sa váš inzerát automaticky deaktivuje a nebude viditeľný pre návštevníkov.
                            </p>
                        </div>
                        
                        <div style="margin: 30px 0; text-align: center;">
                            <a href="{{renew_url}}" style="background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; display: inline-block; box-shadow: 0 4px 15px rgba(255, 193, 7, 0.4);">
                                🔄 Predĺžiť predplatné
                            </a>
                        </div>
                        
                        <div style="margin: 20px 0;">
                            <h3 style="color: #28a745; margin-bottom: 15px;">💡 Výhody predĺženia:</h3>
                            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                                <ul style="margin: 0; padding-left: 20px; line-height: 1.8;">
                                    <li>🌟 Váš inzerát zostane aktívny</li>
                                    <li>👀 Neprerušená viditeľnosť pre návštevníkov</li>
                                    <li>📊 Pokračovanie v zbieraní štatistík</li>
                                    <li>🏆 Udržanie pozície vo vyhľadávaní</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div style="background-color: #ffc107; padding: 20px; text-align: center;">
                    <p style="color: white; margin: 0; font-size: 14px;">© 2025 DiskretneDnes.sk</p>
                </div>
            </div>';
    }

    private function getSubscriptionExpiredTemplate(): string
    {
        return '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff;">
                <div style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); padding: 30px; text-align: center;">
                    <h1 style="color: white; margin: 0; font-size: 28px;">⏰ Predplatné vypršalo</h1>
                    <p style="color: #f8d7da; margin: 10px 0 0 0; font-size: 16px;">Obnovte si služby</p>
                </div>
                
                <div style="padding: 30px; background-color: #fff5f5;">
                    <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-left: 5px solid #dc3545;">
                        <h2 style="color: #333; margin-top: 0;">Ahoj {{user_name}}!</h2>
                        
                        <p style="color: #dc3545; font-size: 18px; font-weight: bold; margin: 20px 0;">
                            😔 Vaše predplatné vypršalo
                        </p>
                        
                        <div style="margin: 20px 0; padding: 20px; background: linear-gradient(135deg, #f8d7da 0%, #fff5f5 100%); border-radius: 8px;">
                            <h3 style="margin: 0 0 15px 0; color: #dc3545;">Detaily inzerátu</h3>
                            <p style="margin: 5px 0; color: #666;"><strong>🎯 ID:</strong> #{{ad_id}}</p>
                            <p style="margin: 5px 0; color: #666;"><strong>📝 Názov:</strong> {{ad_nickname}}</p>
                            <p style="margin: 5px 0; color: #666;"><strong>⏰ Vypršalo:</strong> {{expired_at}}</p>
                        </div>
                        
                        <div style="margin: 20px 0; padding: 15px; background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px;">
                            <p style="margin: 0; color: #856404; font-size: 14px;">
                                ⚠️ <strong>Váš inzerát je momentálne neaktívny</strong> a návštevníci ho nemôžu vidieť.
                            </p>
                        </div>
                        
                        <div style="margin: 30px 0; text-align: center;">
                            <a href="{{renew_url}}" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; display: inline-block; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);">
                                🔄 Obnoviť predplatné
                            </a>
                        </div>
                        
                        <div style="margin: 20px 0;">
                            <h3 style="color: #28a745; margin-bottom: 15px;">🚀 Po obnovení získate:</h3>
                            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                                <ul style="margin: 0; padding-left: 20px; line-height: 1.8;">
                                    <li>✅ Okamžitú aktiváciu inzerátu</li>
                                    <li>👀 Viditeľnosť pre návštevníkov</li>
                                    <li>📊 Obnovenie zbierania štatistík</li>
                                    <li>📧 Upozornenia pred ďalším vypršaním</li>
                                </ul>
                            </div>
                        </div>
                        
                        <p style="color: #666; line-height: 1.6; margin: 20px 0; font-style: italic;">
                            Nestrácajte zákazníkov! Obnovte si predplatné čo najskôr a váš inzerát bude opäť viditeľný.
                        </p>
                    </div>
                </div>
                
                <div style="background-color: #dc3545; padding: 20px; text-align: center;">
                    <p style="color: white; margin: 0; font-size: 14px;">© 2025 DiskretneDnes.sk</p>
                </div>
            </div>';
    }
}