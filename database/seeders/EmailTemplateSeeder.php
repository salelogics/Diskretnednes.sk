<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    public function run()
    {
        $templates = [
            [
                'key' => 'contact',
                'name' => 'Kontaktný formulár',
                'type' => 'admin',  // Opravené z 'system' na 'admin'
                'subject' => 'Nová správa z kontaktného formulára - {{subject}}',
                'content' => $this->getContactTemplate(),
                'variables' => json_encode([
                    'name' => 'Meno odosielateľa',
                    'email' => 'Email odosielateľa',
                    'subject' => 'Predmet správy',
                    'messageContent' => 'Obsah správy',
                    'submittedAt' => 'Čas odoslania'
                ]),
                'is_active' => true
            ],
            [
                'key' => 'ad_report',
                'name' => 'Nahlásenie inzerátu',
                'type' => 'admin',  // Opravené z 'system' na 'admin'
                'subject' => '[VYSOKÁ PRIORITA] Nové nahlásenie inzerátu - DiskretneDnes.sk Admin',
                'content' => $this->getAdReportTemplate(),
                'variables' => json_encode([
                    'ad_id' => 'ID inzerátu',
                    'ad_title' => 'Názov inzerátu',
                    'reason' => 'Dôvod nahlásenia',
                    'details' => 'Detaily nahlásenia',
                    'reporter_email' => 'Email nahlasovateľa',
                    'report_date' => 'Dátum nahlásenia'
                ]),
                'is_active' => true
            ],
            [
                'key' => 'ad_created',
                'name' => 'Nový inzerát - používateľ',
                'type' => 'user',
                'subject' => 'Váš inzerát #{{ad_id}} bol vytvorený - DiskretneDnes.sk',
                'content' => $this->getAdCreatedTemplate(),
                'variables' => json_encode([
                    'user_name' => 'Meno používateľa',
                    'ad_id' => 'ID inzerátu',
                    'ad_nickname' => 'Názov inzerátu',
                    'ad_type' => 'Typ inzerátu',
                    'ad_city' => 'Mesto'
                ]),
                'is_active' => true
            ],
            [
                'key' => 'admin_new_ad',
                'name' => 'Nový inzerát - admin',
                'type' => 'admin',
                'subject' => 'Nový inzerát vytvorený - DiskretneDnes.sk Admin',
                'content' => $this->getAdminNewAdTemplate(),
                'variables' => json_encode([
                    'user_name' => 'Meno používateľa',
                    'ad_id' => 'ID inzerátu',
                    'ad_nickname' => 'Názov inzerátu',
                    'ad_type' => 'Typ inzerátu',
                    'ad_city' => 'Mesto'
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

    private function getContactTemplate(): string
    {
        return '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff;">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;">
                    <h1 style="color: white; margin: 0; font-size: 28px;">📧 Nová správa</h1>
                    <p style="color: #f8f9ff; margin: 10px 0 0 0; font-size: 16px;">z kontaktného formulára</p>
                </div>
                
                <div style="padding: 30px; background-color: #f8f9fa;">
                    <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                        <h2 style="color: #333; margin-top: 0; border-bottom: 2px solid #667eea; padding-bottom: 10px;">
                            📨 {{subject}}
                        </h2>
                        
                        <div style="margin: 20px 0; padding: 15px; background-color: #f1f3f4; border-radius: 8px; border-left: 4px solid #667eea;">
                            <p style="margin: 0; color: #666; font-size: 14px;"><strong>Od:</strong> {{name}} ({{email}})</p>
                            <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;"><strong>Odoslané:</strong> {{submittedAt}}</p>
                        </div>
                        
                        <div style="margin: 20px 0;">
                            <h3 style="color: #333; margin-bottom: 10px;">💬 Správa:</h3>
                            <div style="background: #fff; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; line-height: 1.6;">
                                {{messageContent}}
                            </div>
                        </div>
                        
                        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; text-align: center;">
                            <p style="color: #666; font-size: 12px; margin: 0;">
                                Táto správa bola odoslaná cez kontaktný formulár na stránke DiskretneDnes.sk
                            </p>
                        </div>
                    </div>
                </div>
                
                <div style="background-color: #667eea; padding: 20px; text-align: center;">
                    <p style="color: white; margin: 0; font-size: 14px;">© 2025 DiskretneDnes.sk</p>
                </div>
            </div>';
    }

    private function getAdReportTemplate(): string
    {
        return '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff;">
                <div style="background: linear-gradient(135deg, #ff4757 0%, #c44569 100%); padding: 30px; text-align: center;">
                    <h1 style="color: white; margin: 0; font-size: 28px;">⚠️ NAHLÁSENIE INZERÁTU</h1>
                    <p style="color: #ffe0e0; margin: 10px 0 0 0; font-size: 16px; font-weight: bold;">VYSOKÁ PRIORITA</p>
                </div>
                
                <div style="padding: 30px; background-color: #fff5f5;">
                    <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-left: 5px solid #ff4757;">
                        <h2 style="color: #333; margin-top: 0; color: #ff4757;">
                            🎯 Inzerát #{{ad_id}}: {{ad_title}}
                        </h2>
                        
                        <div style="margin: 20px 0; padding: 15px; background-color: #fff5f5; border-radius: 8px;">
                            <p style="margin: 0; color: #666; font-size: 14px;"><strong>📅 Nahlásené:</strong> {{report_date}}</p>
                            <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;"><strong>📧 Email:</strong> {{reporter_email}}</p>
                        </div>
                        
                        <div style="margin: 20px 0;">
                            <h3 style="color: #ff4757; margin-bottom: 10px;">⚠️ Dôvod nahlásenia:</h3>
                            <div style="background: #fff; padding: 15px; border: 2px solid #ff4757; border-radius: 8px; font-weight: bold; color: #ff4757;">
                                {{reason}}
                            </div>
                        </div>
                        
                        <div style="margin: 20px 0;">
                            <h3 style="color: #333; margin-bottom: 10px;">📝 Detaily:</h3>
                            <div style="background: #f8f9fa; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; line-height: 1.6;">
                                {{details}}
                            </div>
                        </div>
                        
                        <div style="margin-top: 30px; padding: 20px; background: linear-gradient(135deg, #ff4757 0%, #c44569 100%); border-radius: 8px; text-align: center;">
                            <p style="color: white; margin: 0; font-weight: bold;">🚨 VYŽADUJE OKAMŽITÚ POZORNOSŤ 🚨</p>
                            <p style="color: #ffe0e0; margin: 5px 0 0 0; font-size: 14px;">Prosím preverte tento inzerát v najkratšom čase</p>
                        </div>
                    </div>
                </div>
                
                <div style="background-color: #ff4757; padding: 20px; text-align: center;">
                    <p style="color: white; margin: 0; font-size: 14px;">© 2025 DiskretneDnes.sk - Admin Panel</p>
                </div>
            </div>';
    }

    private function getAdCreatedTemplate(): string
    {
        return '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff;">
                <div style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); padding: 30px; text-align: center;">
                    <h1 style="color: white; margin: 0; font-size: 28px;">🎉 Gratulujeme!</h1>
                    <p style="color: #e6fffa; margin: 10px 0 0 0; font-size: 16px;">Váš inzerát bol úspešne vytvorený</p>
                </div>
                
                <div style="padding: 30px; background-color: #f0fff4;">
                    <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-left: 5px solid #48bb78;">
                        <h2 style="color: #333; margin-top: 0;">
                            ✨ Váš inzerát #{{ad_id}}
                        </h2>
                        
                        <div style="margin: 20px 0; padding: 20px; background: linear-gradient(135deg, #e6fffa 0%, #f0fff4 100%); border-radius: 8px;">
                            <h3 style="margin: 0 0 10px 0; color: #38a169;">{{ad_nickname}}</h3>
                            <p style="margin: 5px 0; color: #666;"><strong>🏷️ Typ:</strong> {{ad_type}}</p>
                            <p style="margin: 5px 0; color: #666;"><strong>📍 Mesto:</strong> {{ad_city}}</p>
                        </div>
                        
                        <div style="margin: 20px 0;">
                            <h3 style="color: #38a169; margin-bottom: 15px;">📋 Čo ďalej?</h3>
                            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                                <ul style="margin: 0; padding-left: 20px; line-height: 1.8;">
                                    <li>✅ Váš inzerát bol vytvorený a je aktívny</li>
                                    <li>👀 Návštevníci si ho môžu prezerať</li>
                                    <li>📊 Môžete sledovať štatistiky v profile</li>
                                    <li>✏️ Kedykoľvek ho môžete upraviť</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div style="margin-top: 30px; text-align: center;">
                            <div style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); color: white; padding: 15px 30px; border-radius: 25px; display: inline-block;">
                                <strong>🚀 Želáme veľa úspechov!</strong>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div style="background-color: #48bb78; padding: 20px; text-align: center;">
                    <p style="color: white; margin: 0; font-size: 14px;">© 2025 DiskretneDnes.sk</p>
                </div>
            </div>';
    }

    private function getAdminNewAdTemplate(): string
    {
        return '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff;">
                <div style="background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%); padding: 30px; text-align: center;">
                    <h1 style="color: white; margin: 0; font-size: 28px;">🆕 Nový inzerát</h1>
                    <p style="color: #bee3f8; margin: 10px 0 0 0; font-size: 16px;">v systéme DiskretneDnes.sk</p>
                </div>
                
                <div style="padding: 30px; background-color: #f7fafc;">
                    <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-left: 5px solid #4299e1;">
                        <h2 style="color: #333; margin-top: 0;">
                            📄 Inzerát #{{ad_id}}
                        </h2>
                        
                        <div style="margin: 20px 0; padding: 20px; background: linear-gradient(135deg, #ebf8ff 0%, #f7fafc 100%); border-radius: 8px;">
                            <h3 style="margin: 0 0 10px 0; color: #3182ce;">{{ad_nickname}}</h3>
                            <p style="margin: 5px 0; color: #666;"><strong>👤 Autor:</strong> {{user_name}}</p>
                            <p style="margin: 5px 0; color: #666;"><strong>🏷️ Typ:</strong> {{ad_type}}</p>
                            <p style="margin: 5px 0; color: #666;"><strong>📍 Mesto:</strong> {{ad_city}}</p>
                        </div>
                        
                        <div style="margin: 20px 0;">
                            <h3 style="color: #3182ce; margin-bottom: 15px;">🔧 Admin akcie:</h3>
                            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                                <ul style="margin: 0; padding-left: 20px; line-height: 1.8;">
                                    <li>👁️ Skontrolujte obsah inzerátu</li>
                                    <li>✅ Overte dodržiavanie pravidiel</li>
                                    <li>📊 Sledujte aktivitu inzerátu</li>
                                    <li>⚙️ V prípade potreby upravte alebo odstráňte</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div style="margin-top: 30px; text-align: center;">
                            <div style="background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%); color: white; padding: 15px 30px; border-radius: 25px; display: inline-block;">
                                <strong>📈 Nový obsah v systéme</strong>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div style="background-color: #4299e1; padding: 20px; text-align: center;">
                    <p style="color: white; margin: 0; font-size: 14px;">© 2025 DiskretneDnes.sk - Admin Panel</p>
                </div>
            </div>';
    }
} 