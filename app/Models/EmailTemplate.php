<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'subject',
        'content',
        'variables',
        'type',
        'is_active'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Nahradí premenné v obsahu emailu
     */
    public function renderContent(array $data = []): string
    {
        $content = $this->content;
        
        // Pokús sa parsovať ako Editor.js JSON
        $editorData = $this->parseEditorJsContent($content);
        
        // Ak je to Editor.js formát, konvertuj na HTML
        if ($editorData) {
            $content = $this->convertEditorJsToHtml($editorData);
        }
        
        foreach ($data as $key => $value) {
            // Nahradí {{variable}} s hodnotou
            $content = str_replace('{{' . $key . '}}', $value, $content);
            
            // Nahradí {{ variable }} s hodnotou (s medzerami)
            $content = str_replace('{{ ' . $key . ' }}', $value, $content);
        }
        
        return $content;
    }

    /**
     * Parsuje obsah ako Editor.js JSON
     */
    private function parseEditorJsContent(string $content): ?array
    {
        try {
            $decoded = json_decode($content, true);
            
            // Skontroluj či má štruktúru Editor.js
            if (json_last_error() === JSON_ERROR_NONE && 
                is_array($decoded) && 
                isset($decoded['blocks']) && 
                is_array($decoded['blocks'])) {
                return $decoded;
            }
        } catch (\Exception $e) {
            // Ignoruj chyby parsingu
        }
        
        return null;
    }

    /**
     * Konvertuje Editor.js JSON na HTML
     */
    private function convertEditorJsToHtml(array $editorData): string
    {
        $html = '';
        
        foreach ($editorData['blocks'] as $block) {
            $html .= $this->convertBlockToHtml($block);
        }
        
        return $html;
    }

    /**
     * Konvertuje jeden Editor.js blok na HTML
     */
    private function convertBlockToHtml(array $block): string
    {
        $type = $block['type'] ?? 'paragraph';
        $data = $block['data'] ?? [];
        
        switch ($type) {
            case 'paragraph':
                return '<p>' . ($data['text'] ?? '') . '</p>';
                
            case 'header':
                $level = $data['level'] ?? 2;
                return '<h' . $level . '>' . ($data['text'] ?? '') . '</h' . $level . '>';
                
            case 'list':
                $style = $data['style'] ?? 'unordered';
                $tag = $style === 'ordered' ? 'ol' : 'ul';
                $items = $data['items'] ?? [];
                
                $html = '<' . $tag . '>';
                foreach ($items as $item) {
                    $html .= '<li>' . $item . '</li>';
                }
                $html .= '</' . $tag . '>';
                
                return $html;
                
            case 'quote':
                $text = $data['text'] ?? '';
                $caption = $data['caption'] ?? '';
                return '<blockquote>' . $text . ($caption ? '<cite>' . $caption . '</cite>' : '') . '</blockquote>';
                
            case 'delimiter':
                return '<hr>';
                
            case 'table':
                $content = $data['content'] ?? [];
                $html = '<table border="1" style="border-collapse: collapse; width: 100%;">';
                
                foreach ($content as $row) {
                    $html .= '<tr>';
                    foreach ($row as $cell) {
                        $html .= '<td style="padding: 8px; border: 1px solid #ddd;">' . $cell . '</td>';
                    }
                    $html .= '</tr>';
                }
                
                $html .= '</table>';
                return $html;
                
            case 'image':
                $url = $data['file']['url'] ?? '';
                $caption = $data['caption'] ?? '';
                $html = '<img src="' . htmlspecialchars($url) . '" alt="' . htmlspecialchars($caption) . '" style="max-width: 100%; height: auto;">';
                if ($caption) {
                    $html = '<figure>' . $html . '<figcaption>' . $caption . '</figcaption></figure>';
                }
                return $html;
                
            case 'embed':
                $service = $data['service'] ?? '';
                $embed = $data['embed'] ?? '';
                $width = $data['width'] ?? 580;
                $height = $data['height'] ?? 320;
                
                if ($service === 'youtube') {
                    return '<iframe width="' . $width . '" height="' . $height . '" src="' . htmlspecialchars($embed) . '" frameborder="0" allowfullscreen></iframe>';
                }
                
                return '<div>' . htmlspecialchars($embed) . '</div>';
                
            default:
                // Pre neznáme typy bloku, pokús sa vrátiť text
                return '<p>' . ($data['text'] ?? '') . '</p>';
        }
    }

    /**
     * Nahradí premenné v predmete emailu
     */
    public function renderSubject(array $data = []): string
    {
        $subject = $this->subject;
        
        foreach ($data as $key => $value) {
            $subject = str_replace('{{' . $key . '}}', $value, $subject);
            $subject = str_replace('{{ ' . $key . ' }}', $value, $subject);
        }
        
        return $subject;
    }

    /**
     * Získa template podľa kľúča
     */
    public static function getByKey(string $key): ?self
    {
        return static::where('key', $key)->where('is_active', true)->first();
    }

    /**
     * Získa všetky dostupné premenné pre template
     */
    public function getAvailableVariables(): array
    {
        // Ak je variables null alebo prázdny
        if (empty($this->variables)) {
            return [];
        }

        // Ak je variables už array (cez cast)
        if (is_array($this->variables)) {
            return $this->variables;
        }

        // Ak je variables string, pokús sa ho decode'ovať
        if (is_string($this->variables)) {
            $decoded = json_decode($this->variables, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            
            // Ak nie je validný JSON, vráť prázdny array
            return [];
        }

        // Defaultne vráť prázdny array
        return [];
    }

    /**
     * Získa premenné pre zobrazenie v template (s popismi)
     */
    public function getVariableLabels(): array
    {
        $variables = $this->getAvailableVariables();
        $labels = [];
        
        foreach ($variables as $variable) {
            $labels[$variable] = $this->getVariableLabel($variable);
        }
        
        return $labels;
    }

    /**
     * Získa popisný text pre premennú
     */
    private function getVariableLabel(string $variable): string
    {
        $labels = [
            'user_name' => 'Meno používateľa',
            'user_email' => 'Email používateľa', 
            'ad_id' => 'ID inzerátu',
            'ad_nickname' => 'Názov inzerátu',
            'ad_views' => 'Počet zobrazení',
            'ad_type' => 'Typ inzerátu',
            'ad_city' => 'Mesto',
            'payment_id' => 'ID platby',
            'amount' => 'Suma',
            'due_date' => 'Dátum splatnosti',
            'expiry_date' => 'Dátum vypršania',
            'days_left' => 'Zostáva dní',
            'package_price' => 'Cena balíčku',
            'site_name' => 'Názov stránky',
            'support_url' => 'URL podpory',
            'ticket_id' => 'ID ticketu',
            'ticket_subject' => 'Predmet ticketu',
            'ticket_status' => 'Stav ticketu',
            'ticket_created_at' => 'Dátum vytvorenia ticketu',
        ];

        return $labels[$variable] ?? $variable;
    }
} 