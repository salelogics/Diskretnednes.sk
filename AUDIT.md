# 🔍 Kompletný audit Laravel projektu — Diskretnednes.sk / Erotikon.sk

**Dátum auditu:** 2026-07-02
**Framework:** Laravel 12 (PHP ^8.2)
**Typ projektu:** Inzertný portál pre dospelých (verejné + admin rozhranie, platby Stripe/SMS, Socialite login)

---

## 📊 Zhrnutie (executive summary)

Projekt je funkčný Laravel 12 s modernou skladbou (Livewire, Sanctum, Vite). Audit však odhalil **niekoľko kritických problémov**, ktoré treba riešiť okamžite — najmä:

- **817 MB používateľských uploadov (21 791 súborov) je commitnutých v Gite**, vrátane **verifikačných fotiek** (pravdepodobne doklady totožnosti) → GDPR / únik osobných údajov.
- **Web-accessible PHP skript `public/queue-worker.php`** s hardcoded tokenom → vzdialené spustenie queue / DoS.
- **`public/.deploy_bash_2347.sh`** — deployment skript s cestami servera a `cp -n .env.example .env`, verejne dostupný.
- **`.env.example` obsahuje reálny `APP_KEY`** a je poškodený (UTF-16/BOM binárny obsah).

| Priorita | Počet nálezov |
|----------|---------------|
| 🔴 Kritické | 6 |
| 🟠 Dôležité | 9 |
| 🟡 Odporúčania | 11 |
| 🟢 Nice to have | 6 |

**Veľkosť repozitára:** working tree 827 MB (z toho `public/images/uploads` = **817 MB**), `.git` = **278 MB**, počet commitov = 1 (squashnutá história — dobré, žiadny `.env`/`vendor`/`node_modules` v histórii).

---

# 🔴 KRITICKÉ — riešiť okamžite

### K1. Používateľské uploady (817 MB / 21 791 súborov) sú v Gite — vrátane verifikačných fotiek
**Kde:** `public/images/uploads/**` (`ads/verification/`, `ads/gallery/`, `ads/videos/`, `hero-bg.jpg`, `auth-img.jpg`, …)

Adresár `public/images/uploads` je verzovaný. Obsahuje **verifikačné fotografie** (`ads/verification/*`) — pri portáli tohto typu ide typicky o doklady totožnosti / overovacie selfie. To je vážny únik osobných údajov (GDPR) a zároveň nafukuje repo (817 MB) a `.git` (278 MB). Každý s prístupom ku repu má prístup ku všetkým dokladom.

**Riešenie:**
```bash
# 1) Prestať verzovať uploady (ponechať na disku/serveri)
git rm -r --cached public/images/uploads
# 2) Doplniť do .gitignore
printf '\n/public/images/uploads/\n' >> .gitignore
git add .gitignore && git commit -m "Stop tracking user uploads"
```
- Uploady musia žiť **mimo Gitu** — buď na `storage/app/public` cez symlink, alebo na S3/objektové úložisko (v projekte už je `AWS_*` a `filesystems.php`).
- **Verifikačné fotky by nikdy nemali byť pod `public/`** — presunúť do privátneho disku (`storage/app/private`) a servírovať cez controller s autorizáciou (`Storage::download` + policy).
- Zvážte **prepis histórie** (`git filter-repo --path public/images/uploads --invert-paths`) na odstránenie 817 MB z `.git`. Pozor: prepíše hashe → koordinovať s tímom.
- Pri overovacích dokladoch zvážte oznamovaciu povinnosť podľa GDPR, ak repo bolo zdieľané.

---

### K2. `public/queue-worker.php` — vzdialené spúšťanie queue cez web s hardcoded tokenom
**Kde:** `public/queue-worker.php`

Verejne dostupný endpoint `https://domena/queue-worker.php?token=erotikon-queue-2025` spúšťa `Artisan::call('queue:work')`, `queue:flush`, číta DB. Token je natvrdo v repozitári. Ktokoľvek ho pozná môže spúšťať workery / mazať failed jobs / vyvolať DoS.

**Riešenie:**
```bash
git rm public/queue-worker.php
```
- Queue riešiť **supervisorom** alebo systemd (`php artisan queue:work`), prípadne cron `queue:work --stop-when-empty` bez web endpointu.
- Ak hosting neumožňuje démon, použite cron s CLI, nie verejný PHP súbor.

---

### K3. `public/.deploy_bash_2347.sh` — deployment skript verejne v `public/`
**Kde:** `public/.deploy_bash_2347.sh`

Skript je pod docroot-om a odhaľuje: absolútne cesty servera (`/var/www11/p2832/prosimsi.sk/web/release_...`), verziu PHP, štruktúru release adresárov, `sudo` volania, a hlavne **`cp -n .env.example .env`** — teda ak `.env` neexistuje, produkcia beží z (poškodeného) `.env.example` s commitnutým `APP_KEY`.

**Riešenie:**
```bash
git rm public/.deploy_bash_2347.sh
```
- Deployment artefakty držať mimo `public/` a mimo Gitu.
- Nikdy `cp .env.example .env` v produkcii — `.env` sa spravuje mimo repo (secrets manager / ručne na serveri).

---

### K4. `.env.example` obsahuje reálny `APP_KEY` a je poškodený (UTF-16/BOM)
**Kde:** `.env.example`

Súbor je uložený ako UTF‑16 s BOM (v repo je vidieť „鿿"-artefakty a `਍䄀倀倀…`), teda ho nemožno normálne skopírovať do `.env`. Zároveň obsahuje **hotový `APP_KEY=base64:6FQNp2hUuq0…`**. Príkladový súbor nesmie obsahovať reálny kľúč — ak sa použije (viď K3), všetky inštalácie zdieľajú rovnaký `APP_KEY` (podpis cookies, šifrovanie).

**Riešenie:**
- Zmazať poškodený `.env.example`, ponechať jediný **čistý `env.example` v UTF‑8** (aktuálne existuje `env.example` bez bodky — je v poriadku, premenovať naň).
```bash
git rm .env.example
git mv env.example .env.example   # jeden čistý príklad v UTF-8
```
- V `.env.example` **vyprázdniť `APP_KEY=`** a všetky secrets (len prázdne placeholdery).
- Na serveri vygenerovať nový kľúč: `php artisan key:generate` (a považovať starý za kompromitovaný).

---

### K5. Duplicitné / mätúce example env súbory a `APP_DEBUG=true` v príklade
**Kde:** `.env.example`, `env.example`

Existujú **dva** príkladové súbory. `env.example` má `APP_DEBUG=true` a `APP_ENV=local`, čo pri nesprávnom skopírovaní na produkciu odhalí stack traces a konfiguráciu (Ignition). `.htaccess` síce vypína `display_errors`, ale Laravel Ignition beží nezávisle od PHP `display_errors`.

**Riešenie:**
- Ponechať jeden `.env.example` s bezpečnými defaultmi: `APP_ENV=production`, `APP_DEBUG=false`, prázdne secrets.
- Na produkcii tvrdo overiť `APP_DEBUG=false` (napr. v `deploy.sh` kontrola).

---

### K6. Ad-hoc PHP skripty s bootstrapom aplikácie v repo (`migrate_fix.php`, `dev/*`)
**Kde:** `migrate_fix.php` (root), `dev/find_ad_media.php`, `dev/list_ads_without_photos.php`

Tieto skripty bootstrapujú celý Laravel kernel a manipulujú dáta (`migrate_fix.php` presúva/normalizuje obrázky, mení DB). `migrate_fix.php` je **v roote pod docroot-om** — pri zle nastavenom `.htaccess` môže byť spustiteľný cez web (`https://domena/migrate_fix.php`). To je RCE-like riziko na dátach.

**Riešenie:**
- Prepísať na **Artisan príkazy** (`php artisan make:command`) v `app/Console/Commands` — dostanú autorizáciu, testovateľnosť, žiadne web-vystavenie.
- Do vyriešenia aspoň presunúť mimo docroot a z Gitu:
```bash
git rm migrate_fix.php dev/find_ad_media.php dev/list_ads_without_photos.php
```

---

# 🟠 DÔLEŽITÉ

### D1. Nekonvenčné umiestnenie docroot — `.htaccess` v roote prepisuje do `/public`
**Kde:** `.htaccess` (root)

Docroot je nastavený na **koreň projektu**, nie na `public/`. `.htaccess` „ručne" presmeruje requesty do `public/`. To znamená, že `composer.json`, `.env`, `storage/`, `vendor/`, `routes/` sú fyzicky pod docroot-om a chránené len `FilesMatch`/`RedirectMatch` pravidlami — krehké (napr. `.env.backup`, `.php` skripty ako `migrate_fix.php`, `queue-worker.php` nie sú blokované).

**Riešenie:**
- Ideálne nastaviť docroot hostingu priamo na `public/` a root `.htaccess` zrušiť.
- Ak hosting nedovolí, aspoň rozšíriť blokovanie na `*.php` mimo `public/` a `*.sh`, `*.backup`, a odstrániť všetky spustiteľné súbory z rootu (viď K6).

### D2. `deploy.sh` — žiadne ošetrenie chýb, žiadny build frontendu, žiadny rollback
**Kde:** `deploy.sh`

Problémy:
- Chýba `set -euo pipefail` → pri zlyhaní `composer install`/`migrate` skript pokračuje a nechá web v polrozbitom stave.
- **Chýba `npm ci && npm run build`** → frontend sa nebuildí (spolieha sa na commitnutý `public/build`, viď D3).
- **Chýba rollback** pri zlyhaní migrácií/cache.
- **Chýba reštart queue/scheduler** (`queue:restart`).
- Nekonzistentné permissions (`755` vs `775`), spúšťa sa priamo v live adresári (nie atomic release).

**Riešenie — bezpečnejší, atomický deploy s rollbackom:** viď návrh v sekcii *Deployment* nižšie.

### D3. Buildnuté assety `public/build/*` sú commitnuté v Gite
**Kde:** `public/build/assets/*` (app JS 460 KB, CSS 244 KB, remixicon .svg 2.7 MB, .eot/.ttf/.woff…)

Buildy patria do CI/deploy, nie do Gitu — spôsobujú konflikty, bloat a riziko, že nasadený build nezodpovedá zdrojom.

**Riešenie:**
```bash
git rm -r --cached public/build
echo '/public/build' >> .gitignore
```
Build robiť v deployi (`npm ci && npm run build`). `public/hot` a `public/storage` už v `.gitignore` sú — dobré.

### D4. Nepoužívané NPM balíčky — 4 kompletné editory navyše (~desiatky MB)
**Kde:** `package.json`

Reálne sa importuje iba **EditorJS** (`resources/js/components/editor.js`) + Alpine + axios + remixicon. Nasledujúce sú deklarované, ale **nikde v `resources/` sa nepoužívajú** (0 referencií):

- `@ckeditor/ckeditor5-build-classic`, `@ckeditor/ckeditor5-language`
- `@tinymce/tinymce-vue`, `tinymce`
- `@tiptap/core`, `@tiptap/pm`, `@tiptap/starter-kit`, `@tiptap/extension-image`, `@tiptap/extension-link`, `@tiptap/extension-underline`, `@tiptap/extension-youtube`
- `quill`, `tiptap-markdown`
- `@vitejs/plugin-vue` (žiadne `.vue` súbory v projekte)
- `@tailwindcss/vite` `^4.0.0` (nepoužitý vo `vite.config.js`; projekt beží na Tailwind **v3** cez PostCSS → konflikt verzií)

**Riešenie:**
```bash
npm remove @ckeditor/ckeditor5-build-classic @ckeditor/ckeditor5-language \
  @tinymce/tinymce-vue tinymce quill tiptap-markdown \
  @tiptap/core @tiptap/pm @tiptap/starter-kit @tiptap/extension-image \
  @tiptap/extension-link @tiptap/extension-underline @tiptap/extension-youtube \
  @vitejs/plugin-vue @tailwindcss/vite
```
(Najprv over, či niektorý editor nepoužíva admin cez CDN/inline — grep nič nenašiel.)

### D5. Konflikt Tailwind v3 vs v4
**Kde:** `package.json`, `postcss.config.js`, `tailwind.config.js`

`tailwindcss@^3.1.0` (v3, konfig cez `tailwind.config.js` + `postcss.config.js`) spolu s `@tailwindcss/vite@^4.0.0` (v4 architektúra). Miešanie vedie k nepredvídateľnému buildu.

**Riešenie:** rozhodnúť sa pre jednu verziu. Odporúčam ostať na v3 (existujúci config) a odstrániť `@tailwindcss/vite`, alebo migrovať na v4 podľa upgrade guideu (potom zmazať `postcss.config.js` a `tailwind.config.js` obsah presunúť do CSS `@theme`).

### D6. Overovacie/citlivé súbory pod verejným `public/` (aj mimo Gitu)
**Kde:** `public/images/uploads/ads/verification/*`

Aj po odstránení z Gitu (K1) zostáva architektonický problém: verifikačné doklady sú servírované ako **verejné statické súbory** (predvídateľné cesty typu `verification_49801.jpg`). Ktokoľvek s URL k nim pristúpi bez autentifikácie.

**Riešenie:** presunúť na privátny disk (`storage/app/private/verifications`), servírovať cez route s `auth` + policy, generovať dočasné signed URL (`Storage::temporaryUrl`).

### D7. `public/test.txt` — debug leftover vo verejnom priečinku
**Kde:** `public/test.txt` (obsah: `OK`)

Testovací súbor v produkcii. Drobnosť, ale patrí preč.
**Riešenie:** `git rm public/test.txt`

### D8. `console.log()` v produkčnom frontende (68 výskytov)
**Kde:** `resources/js/multi-step-form.js` (10×), `resources/js/components/editor.js` (1×) a **68× v blade šablónach** (`resources/views/**`, napr. `ads/edit.blade.php`, `pages/ad-detail.blade.php`, `admin/blog/*`, `components/*`).

Ladiace výpisy v produkcii môžu odhaľovať interné dáta a špinia konzolu.

**Riešenie:**
- Odstrániť ladiace `console.log`, alebo ich strhnúť pri builde: vo `vite.config.js` pridať `esbuild: { drop: ['console', 'debugger'] }` (len pre produkčný build).
- Inline `<script>console.log>` v blade presunúť do JS modulov.

### D9. Nemám overené aktuálnosti/zraniteľnosti závislostí (treba spustiť)
**Kde:** `composer.lock`, `package-lock.json`

Audit bežal v prostredí bez prístupu k balíčkovým registrom, takže `composer outdated` / `npm audit` nebolo možné spustiť. Sklad je moderný (Laravel 12, PHP 8.2), no treba pravidelnú kontrolu.

**Riešenie — spustiť lokálne/CI:**
```bash
composer audit                 # security advisories
composer outdated --direct     # zastarané priame závislosti
npm audit --production
npm outdated
```
(Automatizovať cez GitHub Actions — viď sekcia GitHub.)

---

# 🟡 ODPORÚČANIA

### O1. TODO/FIXME v kóde (10 výskytov)
`app/Console/Commands/CheckQueue.php`, `app/Console/Commands/FixImagePaths.php`, `app/Http/Controllers/Admin/ArticleController.php`. Prejsť a doriešiť/založiť issue.

### O2. `SetStripeKeys.php` obsahuje fake test kľúč v kóde
`app/Console/Commands/SetStripeKeys.php:21` — `sk_test_512345…` placeholder. Neškodné (fake), ale mätúce; radšej načítať z prompt/env.

### O3. Konsolidovať konfiguráciu `.gitignore`
Aktuálny `.gitignore` je slušný, no chýbajú: `/public/build`, `/public/images/uploads`, `*.zip` už je, doplniť `/dev`, `/*.dump`, `/*.sqlite` (okrem migrácií). Návrh kompletného `.gitignore` je v sekcii GitHub.

### O4. Optimalizácia veľkých obrázkov / assetov
- `hero-bg.jpg`, `auth-img.jpg` majú po **16 MB** — to sú neoptimalizované originály. Skomprimovať (WebP/AVIF, `mozjpeg`), cieľ < 300 KB pre hero.
- `remixicon.svg` (2.7 MB) sa dodáva celý — zvážiť subsetting alebo iba `woff2`.
- Mnoho gallery/verification obrázkov 0.5–3 MB → zaviesť pipeline na resize + WebP pri uploade (`intervention/image`).

### O5. Servírovanie fontov: Google Fonts cez `@import` v CSS
`resources/css/app.css` načítava Google Fonts cez `@import url(...)` → render-blocking + externá závislosť + GDPR (IP na Google). Self-hostovať fonty (napr. cez `@fontsource`) alebo `<link rel=preconnect>` + `display=swap`.

### O6. `remixicon` sa importuje celý (ikonový font ~1 MB naprieč formátmi)
Zvážiť prechod na SVG sprity iba pre reálne používané ikony (výrazné zmenšenie CSS/fontov).

### O7. Zjednotiť naming a odstrániť `dev/` z produkcie
Analytické skripty (`dev/*`) prepísať na Artisan príkazy s `--dry-run` a testami (súvisí s K6).

### O8. Cache stratégia v produkcii
`env.example` má `CACHE_STORE=database`, `SESSION_DRIVER=database`, `QUEUE_CONNECTION=database`. Pri väčšej záťaži prejsť na **Redis** (v configu už pripravený `REDIS_*`) pre cache/session/queue.

### O9. Pridať `php artisan optimize` do deployu
Namiesto samostatných `config:cache`/`route:cache`/`view:cache` použiť `php artisan optimize` (+ `event:cache`), a v deployi **`config:clear` až po** nasadení novej `.env`.

### O10. Pridať statickú analýzu a coding standard do CI
`laravel/pint` je už v `require-dev` — pridať `pint --test` do CI. Zvážiť Larastan/PHPStan.

### O11. Testy — rozšíriť pokrytie
`tests/Feature`, `tests/Unit` existujú (12 súborov). Pri platbách (Stripe/SMS) a verifikácii doplniť feature testy; `phpunit.xml` je korektne nastavený (sqlite `:memory:`).

---

# 🟢 NICE TO HAVE

### N1. `.editorconfig` už existuje — dobré. Doplniť `CONTRIBUTING.md`.
### N2. Pridať `README.md` s inštrukciami setup/deploy (aktuálne chýba).
### N3. Dependabot / Renovate pre automatické PR na updaty závislostí.
### N4. Pridať `LICENSE` kontrolu — repo má MIT z Laravel skeletonu; pri komerčnom projekte zvážiť proprietárnu licenciu.
### N5. Health-check endpoint (`/up` Laravel 11+ už je) napojiť na monitoring.
### N6. Lazy-loading / code-splitting EditorJS (načítať iba na admin stránkach s editorom, nie v `app.js` globálne).

---

# 📦 Detailné sekcie podľa zadania

## 1. Git — čo nemá byť vo verzii

| Kategória | Stav | Poznámka |
|-----------|------|----------|
| `node_modules` | ✅ ignorované | OK |
| `vendor` | ✅ ignorované | OK |
| cache (`bootstrap/cache`, views) | ✅ ignorované | OK |
| build (`public/build`) | 🔴 **commitnuté** | viď D3 |
| logy (`*.log`, `storage/logs`) | ✅ ignorované | OK |
| **uploady** (`public/images/uploads`) | 🔴 **commitnuté, 817 MB** | viď K1 |
| ZIP | ✅ ignorované (`*.zip`) | OK |
| veľké binárky | 🔴 v uploads + build | viď K1/D3 |
| `.env` | ✅ ignorované | žiadny `.env` v histórii |
| ad-hoc skripty (`dev/`, `migrate_fix.php`) | 🔴 commitnuté | viď K6 |

**Rekapitulácia histórie:** iba 1 commit, žiadny `.env`/`vendor`/`node_modules` nikdy nebol commitnutý (dobré). Bloat pochádza z uploadov a buildov v aktuálnom strome.

### TOP 50 najväčších verzovaných súborov
```
16M   public/images/uploads/hero-bg.jpg
16M   public/images/uploads/auth-img.jpg
16M   public/images/uploads/ads/verification/1748272024_6834839891a3b.jpg
16M   public/images/uploads/ads/verification/1748269850_68347b1a2d2c9.jpg
16M   public/images/uploads/ads/gallery/1748272024_6834839891fa4_1.jpg
16M   public/images/uploads/ads/gallery/1748272024_6834839891d46_0.jpg
16M   public/images/uploads/ads/gallery/1748269850_68347b1a2d8d5_1.jpg
16M   public/images/uploads/ads/gallery/1748269850_68347b1a2d5c4_0.jpg
5.9M  public/images/uploads/ads/videos/video_1752413780_6873b654a4bd2.mp4
3.3M  public/images/uploads/ads/gallery/gallery_1758004286_68c9043e78838.jpeg
2.7M  public/build/assets/remixicon-C2wQ2gtc.svg
2.6M  public/images/uploads/ads/verification/verification_50692.jpg
2.3M  public/images/uploads/ads/gallery/gallery_1758004286_68c9043e788ad.jpeg
1.9M  public/images/uploads/ads/gallery/gallery_1758004286_68c9043e7821b.jpeg
1.4M  public/images/uploads/ads/gallery/gallery_49800.jpg
1.3M  public/images/uploads/ads/verification/verification_1755498113_68a2c681a7a97.jpeg
1.2M  public/images/uploads/ads/gallery/gallery_49805.jpg
1.1M  public/images/uploads/ads/gallery/gallery_49804.jpg
1008K public/images/uploads/ads/verification/verification_1758004286_68c9043e753a0.jpeg
1008K public/images/uploads/ads/gallery/gallery_1758004286_68c9043e76fc7.jpeg
824K  public/images/uploads/images/clubs/1753166832_authbgjpg.jpg
824K  public/images/uploads/ads/gallery/gallery_1752413780_6873b654a48c1.jpg
808K  public/images/uploads/ads/gallery/gallery_49799.jpg
684K  public/images/uploads/ads/verification/verification_1756156023_68acd07700151.png
684K  public/images/uploads/ads/gallery/gallery_1756156023_68acd07713436.png
656K  public/images/uploads/ads/verification/verification_49801.jpg
656K  public/images/uploads/ads/gallery/gallery_49801.jpg
648K  public/images/uploads/ads/verification/verification_1757149865_68bbfaa909150.jpg
648K  public/images/uploads/ads/gallery/gallery_1757149865_68bbfaa90b32c.jpg
596K  public/images/uploads/ads/gallery/gallery_49802.jpg
580K  public/images/uploads/ads/verification/verification_1757329816_68beb9988e38e.png
560K  public/build/assets/remixicon-CfJD46dY.ttf
560K  public/build/assets/remixicon-BVJ9S1ev.eot
476K  public/images/uploads/ads/verification/verification_1756296631_68aef5b78019a.png
476K  public/images/uploads/ads/gallery/gallery_1756296631_68aef5b781838.png
472K  public/images/uploads/ads/gallery/gallery_1757330616_68bebcb85b7b4.png
472K  package-lock.json
464K  public/images/uploads/ads/gallery/gallery_1757328738_68beb5626a40e.png
460K  public/build/assets/app-BDz79mw5.js
456K  public/images/uploads/ads/verification/verification_50671.jpg
448K  public/images/uploads/ads/gallery/gallery_1756156023_68acd077205fe.png
432K  public/images/uploads/ads/gallery/gallery_49803.jpg
428K  public/images/uploads/ads/verification/verification_1757330616_68bebcb85b6b8.png
420K  public/images/uploads/ads/verification/verification_1757331157_68bebed528b33.png
420K  public/images/uploads/ads/verification/verification_1757328738_68beb56269c67.png
392K  public/images/uploads/ads/gallery/gallery_1757329505_68beb861cb267.png
388K  public/images/uploads/ads/gallery/gallery_1757329505_68beb861cad75.png
380K  public/images/uploads/ads/gallery/gallery_50672.jpg
376K  public/images/uploads/ads/verification/verification_1757330823_68bebd87da1e3.png
376K  composer.lock
```
> **Pozorovanie:** 49 z 50 najväčších súborov sú uploady/buildy — všetky patria mimo Gitu.

## 2. Laravel — štruktúra a best practices

| Prvok | Stav |
|-------|------|
| `composer.json` | ✅ moderný (Laravel 12, PHP 8.2, sort-packages, optimize-autoloader) |
| `package.json` | 🟠 veľa nepoužívaných balíčkov (D4), Tailwind v3/v4 konflikt (D5) |
| `deploy.sh` | 🟠 bez error-handlingu, bez buildu, bez rollbacku (D2) |
| `artisan` | ✅ štandardný |
| `storage` | ⚠️ symlink `storage -> ../shared/storage` (shared-hosting pattern; OK ak `shared/` existuje a je mimo docroot) |
| `bootstrap/cache` | ✅ ignorované okrem `.gitkeep` |
| symbolické linky | `storage` (viac vyššie); `public/storage` ignorované ✅ |
| `.env.example` | 🔴 poškodený + reálny APP_KEY (K4/K5) |

**Best practices – zhrnutie:** kód má poriadok (`Services`, `Traits`, `Requests`, `Livewire`, `Policies` implicitne). Hlavné odchýlky: docroot nie je `public/` (D1), ad-hoc skripty v roote (K6), citlivé súbory verejné (D6).

## 3. Deployment — návrh bezpečnejšieho `deploy.sh`

```bash
#!/usr/bin/env bash
set -euo pipefail

# === Konfigurácia ===
APP_DIR="/var/www/erotikon"
RELEASES="$APP_DIR/releases"
SHARED="$APP_DIR/shared"           # .env, storage, uploads žijú tu (mimo release)
CURRENT="$APP_DIR/current"
KEEP=5
TS="$(date +%Y%m%d%H%M%S)"
NEW="$RELEASES/$TS"

log(){ printf '\n\033[1;34m▶ %s\033[0m\n' "$1"; }
rollback(){ echo "❌ Zlyhalo — rollback"; [ -L "$CURRENT" ] && ln -sfn "$(readlink "$CURRENT")" "$CURRENT"; rm -rf "$NEW"; exit 1; }
trap rollback ERR

log "Checkout kódu"
git clone --depth 1 --branch main "$REPO_URL" "$NEW"
cd "$NEW"

log "Symlinky na zdieľané (mimo release)"
ln -sfn "$SHARED/.env"     "$NEW/.env"
rm -rf "$NEW/storage" && ln -sfn "$SHARED/storage" "$NEW/storage"
ln -sfn "$SHARED/uploads" "$NEW/public/images/uploads"

log "Composer (bez dev, optimalizovaný)"
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-progress

log "Frontend build"
npm ci
npm run build

log "Overenie prostredia"
php artisan config:clear
[ "$(php artisan tinker --execute='echo (int) config("app.debug");')" = "0" ] || { echo "APP_DEBUG musí byť false"; exit 1; }

log "Migrácie"
php artisan migrate --force

log "Cache"
php artisan optimize        # config + routes + views + events
php artisan storage:link || true

log "Permissions"
chmod -R ug+rwX "$SHARED/storage" "$NEW/bootstrap/cache"

log "Atomický prepis symlinku"
ln -sfn "$NEW" "$CURRENT"

log "Reštart workerov"
php "$CURRENT/artisan" queue:restart
# supervisorctl restart erotikon-worker:*  # ak beží supervisor

log "Upratanie starých release"
ls -1dt "$RELEASES"/*/ | tail -n +$((KEEP+1)) | xargs -r rm -rf

trap - ERR
echo "✅ Deploy $TS hotový"
```

**Kľúčové zlepšenia oproti súčasnému `deploy.sh`:**
- **Atomické release + symlink `current`** → nulový downtime a **okamžitý rollback** (`ln -sfn` na predošlý release).
- `set -euo pipefail` + `trap ... ERR` → pri chybe sa nezmení produkcia.
- **`npm ci && npm run build`** priamo v deployi (žiadny build v Gite).
- `.env`, `storage`, `uploads` sú **zdieľané mimo release** → prežijú deploy.
- Overenie `APP_DEBUG=false` pred migráciami.
- `queue:restart` + odporúčanie supervisora.
- **Scheduler:** na server pridať jediný cron:
  ```
  * * * * * cd /var/www/erotikon/current && php artisan schedule:run >> /dev/null 2>&1
  ```
- **Queue (supervisor):**
  ```ini
  [program:erotikon-worker]
  command=php /var/www/erotikon/current/artisan queue:work --sleep=3 --tries=3 --max-time=3600
  autostart=true
  autorestart=true
  numprocs=2
  ```

## 4. Frontend — Vite / assets

- **Vite config:** korektný (`laravel-vite-plugin`, hash filenames, HMR). Pridať produkčné strhnutie konzoly: `esbuild: { drop: ['console','debugger'] }`, prípadne `build.chunkSizeWarningLimit` a `manualChunks` pre EditorJS.
- **`public/build`:** momentálne v Gite → odstrániť (D3), buildovať v CI/deploy.
- **Tailwind:** v3 vs v4 konflikt (D5). Content paths sú v poriadku (blade + js + storage views).
- **JS:** iba `app.js` (Alpine, axios, EditorJS, multi-step-form). EditorJS načítaný globálne → code-split na admin (N6).
- **CSS:** `app.css` = Tailwind + remixicon + Google Fonts `@import` (render-blocking, GDPR – O5).
- **Optimalizácie:** self-host fonty, WebP/AVIF pre hero (16 MB!), subsetting remixicon, lazy-load editor. Odstrániť 4 nepoužívané editory (D4) → dramaticky menší `node_modules` a rýchlejší build.

## 5. Bezpečnosť

| Oblasť | Nález |
|--------|-------|
| secrets / API keys | ✅ v `config/*` všetko cez `env()` (services.php čisté). 🔴 ale reálny `APP_KEY` v `.env.example` (K4) |
| `.env` | ✅ ignorovaný, nie v histórii. 🔴 `cp .env.example .env` pattern (K3) |
| debug | 🔴 `APP_DEBUG=true` v `env.example` (K5); `console.log` v prod (D8) |
| storage / uploady | 🔴 verifikačné doklady verejné + v Gite (K1/D6) |
| permissions | 🟠 `deploy.sh` nekonzistentné (D2) |
| executable files | ✅ žiadne tracked súbory s mode 755; 🔴 ale `public/*.sh`, `public/queue-worker.php`, `migrate_fix.php` (K2/K3/K6) |
| backup súbory | ✅ žiadne `.sql/.bak/.zip/.dump` v Gite; `.env.backup`/`/backups` ignorované |
| session | ✅ `http_only=true`, `same_site=lax`, `secure` cez env → nastaviť `SESSION_SECURE_COOKIE=true` na prod |

## 6. Výkon

- **Duplicitné/nepoužívané balíčky:** 4 WYSIWYG editory navyše (CKEditor, TinyMCE, Tiptap, Quill) + Vue plugin bez `.vue` + `@tailwindcss/vite` (D4/D5).
- **Nepoužívané JS/CSS:** dead editor importy nie sú (EditorJS sa reálne používa), ale bundle nesie remixicon celý; strhnutie konzoly.
- **Veľké obrázky:** `hero-bg.jpg`/`auth-img.jpg` po 16 MB, množstvo uploadov > 1 MB → resize pipeline + WebP (O4).
- **Veľké assety:** `remixicon.svg` 2.7 MB, `app.js` 460 KB → code-split.
- **DB drivers pre cache/queue/session** → Redis pri raste (O8).

## 7. Composer

- Skladba moderná a čistá (Laravel 12, Sanctum 4, Socialite 5, Stripe 17, dompdf 3, livewire 3).
- `composer audit` / `composer outdated` sa v tomto prostredí nedali spustiť (bez registra) → **spustiť v CI** (D9). Žiadne zjavné konflikty v `composer.json`; `allow-plugins` explicitne povolené (dobré).
- `require-dev` obsahuje `pest-plugin` allow, ale Pest nie je v require → neškodné.

## 8. NPM

- **Zastarané/audit:** `npm audit` / `npm outdated` spustiť v CI (D9).
- **Unused:** viď D4 (15 balíčkov na odstránenie).
- Po vyčistení znovu vygenerovať `package-lock.json` (`npm install`).

## 9. Kód

| Hľadané | Výsledok |
|---------|----------|
| `dd()` / `dump()` / `var_dump()` / `ddd()` / `ray()` | ✅ **0** v `app/` |
| `console.log()` | 🟠 68× v blade + 11× v JS (D8) |
| `TODO` / `FIXME` | 🟡 10× v 3 súboroch (O1) |
| dead code / ad-hoc skripty | 🔴 `migrate_fix.php`, `dev/*` (K6) |
| duplicate code | drobné (analytické skripty duplikujú model logiku) → Artisan príkazy |

> Pozitívne: žiadne `dd()/dump()/var_dump()` v aplikačnom kóde — čisté.

## 10. GitHub — návrhy

### Lepší `.gitignore` (doplnky)
```gitignore
# Build & uploads (nikdy do Gitu)
/public/build
/public/hot
/public/storage
/public/images/uploads

# Ad-hoc / dev skripty
/dev
/migrate_fix.php

# Zálohy, dumpy
*.zip
*.sql
*.dump
*.sqlite
/backups
/storage/app/backups
```

### GitHub Actions — CI (`.github/workflows/ci.yml`)
```yaml
name: CI
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with: { php-version: '8.2', coverage: none }
      - run: composer install --prefer-dist --no-progress
      - run: cp env.example .env && php artisan key:generate
      - run: vendor/bin/pint --test        # coding standard
      - run: composer audit                 # security advisories
      - run: php artisan test               # sqlite :memory:
  frontend:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with: { node-version: '22', cache: 'npm' }
      - run: npm ci
      - run: npm audit --production || true
      - run: npm run build
```

### Deployment cez SSH (`.github/workflows/deploy.yml`)
```yaml
name: Deploy
on:
  release:
    types: [published]
jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: appleboy/ssh-action@v1
        with:
          host: ${{ secrets.SSH_HOST }}
          username: ${{ secrets.SSH_USER }}
          key: ${{ secrets.SSH_KEY }}
          script: cd /var/www/erotikon && ./deploy.sh
```
(Secrets `SSH_HOST/SSH_USER/SSH_KEY` v GitHub → Settings → Secrets. Nikdy nie v repo.)

### Release workflow
- Semantic version tagy (`v1.2.0`), `release` trigger spúšťa deploy.
- Voliteľne changelog cez `release-please` alebo `git-cliff`.
- Dependabot (`.github/dependabot.yml`) pre `composer` + `npm` (N3).

## 11. Výstup
Tento súbor (`AUDIT.md`) je štruktúrovaný podľa priorít 🔴/🟠/🟡/🟢 s konkrétnym riešením ku každému nálezu (vyššie).

---

## ✅ Odporúčaný poradník krokov (quick-win poradie)

1. **Zmazať** `public/queue-worker.php`, `public/.deploy_bash_2347.sh`, `public/test.txt`, `migrate_fix.php`, `dev/*` (K2/K3/K6/D7).
2. **Vyčistiť `.env.example`** (bez reálneho `APP_KEY`, `APP_DEBUG=false`) a zjednotiť na jeden UTF-8 súbor (K4/K5).
3. **Rotovať `APP_KEY`** a všetky secrets, ktoré mohli byť zdieľané (K4).
4. **Prestať verzovať** `public/images/uploads` a `public/build`; presunúť verifikačné fotky na privátny disk (K1/D3/D6).
5. **Prepísať `deploy.sh`** na atomický deploy s rollbackom + `npm run build` + `queue:restart` (D2).
6. **Odstrániť nepoužívané NPM balíčky** a vyriešiť Tailwind v3/v4 (D4/D5).
7. **Nastaviť CI** (test, pint, composer/npm audit) a SSH deploy (sekcia 10).
8. Optimalizovať obrázky a fonty (O4/O5).
