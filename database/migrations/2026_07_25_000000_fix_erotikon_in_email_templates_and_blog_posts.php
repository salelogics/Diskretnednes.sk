<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * email_templates rows are only ever written by EmailTemplateSeeder /
     * MissingEmailTemplatesSeeder, and the deploy pipeline never runs
     * db:seed (only migrate). The seeder source was already updated to
     * "DiskretneDnes.sk" during the rebrand, but the live rows were seeded
     * long before that and were never refreshed, so admin/email-templates
     * still showed subjects like "... - Erotikon.sk".
     *
     * blog_posts is the other free-form admin-authored content table -
     * articles written before the rebrand may still mention the old brand
     * in their title/excerpt/content. slug is deliberately left untouched
     * since changing it would break any already-shared/indexed post URL.
     *
     * Sweeps every row of both tables still containing "erotikon" and
     * swaps the brand strings in place, same approach as the settings
     * table fix (2026_07_18_000000), so any admin customization of the
     * surrounding text is preserved.
     */
    public function up(): void
    {
        $replacements = [
            'https://www.erotikon.sk' => 'https://www.diskretnednes.sk',
            'https://erotikon.sk' => 'https://diskretnednes.sk',
            'www.erotikon.sk' => 'www.diskretnednes.sk',
            'Erotikon.sk' => 'DiskretneDnes.sk',
            'EROTIKON.SK' => 'DISKRETNEDNES.SK',
            'erotikon.sk' => 'diskretnednes.sk',
            'Erotikon' => 'DiskretneDnes',
            'erotikon' => 'diskretnednes',
        ];

        if (Schema::hasTable('email_templates')) {
            $this->sweepTable('email_templates', ['name', 'subject', 'content'], $replacements);
        }

        if (Schema::hasTable('blog_posts')) {
            $this->sweepTable('blog_posts', ['title', 'excerpt', 'content'], $replacements);
        }
    }

    private function sweepTable(string $table, array $columns, array $replacements): void
    {
        $rows = DB::table($table)
            ->where(function ($query) use ($columns) {
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', '%erotikon%');
                }
            })
            ->get(array_merge(['id'], $columns));

        foreach ($rows as $row) {
            $update = [];

            foreach ($columns as $column) {
                $value = $row->{$column};

                if ($value === null) {
                    continue;
                }

                $newValue = str_replace(array_keys($replacements), array_values($replacements), $value);

                if ($newValue !== $value) {
                    $update[$column] = $newValue;
                }
            }

            if (!empty($update)) {
                DB::table($table)->where('id', $row->id)->update($update);
            }
        }
    }

    public function down(): void
    {
        // Intentionally irreversible - original seeded/admin-edited values aren't recorded.
    }
};
