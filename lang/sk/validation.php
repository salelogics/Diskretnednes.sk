<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Pole :attribute musí byť akceptované.',
    'accepted_if' => 'Pole :attribute musí byť akceptované, keď :other je :value.',
    'active_url' => 'Pole :attribute nie je platná URL adresa.',
    'after' => 'Pole :attribute musí byť dátum po :date.',
    'after_or_equal' => 'Pole :attribute musí byť dátum po alebo rovný :date.',
    'alpha' => 'Pole :attribute môže obsahovať len písmená.',
    'alpha_dash' => 'Pole :attribute môže obsahovať len písmená, čísla, pomlčky a podčiarkovníky.',
    'alpha_num' => 'Pole :attribute môže obsahovať len písmená a čísla.',
    'array' => 'Pole :attribute musí byť pole.',
    'ascii' => 'Pole :attribute môže obsahovať len jednobytové alfanumerické znaky a symboly.',
    'before' => 'Pole :attribute musí byť dátum pred :date.',
    'before_or_equal' => 'Pole :attribute musí byť dátum pred alebo rovný :date.',
    'between' => [
        'array' => 'Pole :attribute musí mať medzi :min a :max položkami.',
        'file' => 'Pole :attribute musí mať medzi :min a :max kilobajtov.',
        'numeric' => 'Pole :attribute musí byť medzi :min a :max.',
        'string' => 'Pole :attribute musí mať medzi :min a :max znakmi.',
    ],
    'boolean' => 'Pole :attribute musí byť true alebo false.',
    'can' => 'Pole :attribute obsahuje neautorizovanú hodnotu.',
    'confirmed' => 'Potvrdenie poľa :attribute sa nezhoduje.',
    'current_password' => 'Heslo je nesprávne.',
    'date' => 'Pole :attribute nie je platný dátum.',
    'date_equals' => 'Pole :attribute musí byť dátum rovný :date.',
    'date_format' => 'Pole :attribute sa nezhoduje s formátom :format.',
    'decimal' => 'Pole :attribute musí mať :decimal desatinných miest.',
    'declined' => 'Pole :attribute musí byť odmietnuté.',
    'declined_if' => 'Pole :attribute musí byť odmietnuté, keď :other je :value.',
    'different' => 'Pole :attribute a :other musia byť odlišné.',
    'digits' => 'Pole :attribute musí mať :digits číslic.',
    'digits_between' => 'Pole :attribute musí mať medzi :min a :max číslicami.',
    'dimensions' => 'Pole :attribute má neplatné rozmery obrázka.',
    'distinct' => 'Pole :attribute má duplicitnú hodnotu.',
    'doesnt_end_with' => 'Pole :attribute nesmie končiť jedným z nasledujúcich: :values.',
    'doesnt_start_with' => 'Pole :attribute nesmie začínať jedným z nasledujúcich: :values.',
    'email' => 'Pole :attribute musí byť platná emailová adresa.',
    'ends_with' => 'Pole :attribute musí končiť jedným z nasledujúcich: :values.',
    'enum' => 'Vybraná hodnota pre :attribute je neplatná.',
    'exists' => 'Vybraná hodnota pre :attribute je neplatná.',
    'file' => 'Pole :attribute musí byť súbor.',
    'filled' => 'Pole :attribute musí mať hodnotu.',
    'gt' => [
        'array' => 'Pole :attribute musí mať viac ako :value položiek.',
        'file' => 'Pole :attribute musí byť väčšie ako :value kilobajtov.',
        'numeric' => 'Pole :attribute musí byť väčšie ako :value.',
        'string' => 'Pole :attribute musí mať viac ako :value znakov.',
    ],
    'gte' => [
        'array' => 'Pole :attribute musí mať :value alebo viac položiek.',
        'file' => 'Pole :attribute musí byť väčšie alebo rovné :value kilobajtom.',
        'numeric' => 'Pole :attribute musí byť väčšie alebo rovné :value.',
        'string' => 'Pole :attribute musí mať :value alebo viac znakov.',
    ],
    'image' => 'Pole :attribute musí byť obrázok.',
    'in' => 'Vybraná hodnota pre :attribute je neplatná.',
    'in_array' => 'Pole :attribute neexistuje v :other.',
    'integer' => 'Pole :attribute musí byť celé číslo.',
    'ip' => 'Pole :attribute musí byť platná IP adresa.',
    'ipv4' => 'Pole :attribute musí byť platná IPv4 adresa.',
    'ipv6' => 'Pole :attribute musí byť platná IPv6 adresa.',
    'json' => 'Pole :attribute musí byť platný JSON reťazec.',
    'lowercase' => 'Pole :attribute musí byť malými písmenami.',
    'lt' => [
        'array' => 'Pole :attribute musí mať menej ako :value položiek.',
        'file' => 'Pole :attribute musí byť menšie ako :value kilobajtov.',
        'numeric' => 'Pole :attribute musí byť menšie ako :value.',
        'string' => 'Pole :attribute musí mať menej ako :value znakov.',
    ],
    'lte' => [
        'array' => 'Pole :attribute nesmie mať viac ako :value položiek.',
        'file' => 'Pole :attribute musí byť menšie alebo rovné :value kilobajtom.',
        'numeric' => 'Pole :attribute musí byť menšie alebo rovné :value.',
        'string' => 'Pole :attribute musí mať :value alebo menej znakov.',
    ],
    'mac_address' => 'Pole :attribute musí byť platná MAC adresa.',
    'max' => [
        'array' => 'Pole :attribute nesmie mať viac ako :max položiek.',
        'file' => 'Pole :attribute nesmie byť väčšie ako :max kilobajtov.',
        'numeric' => 'Pole :attribute nesmie byť väčšie ako :max.',
        'string' => 'Pole :attribute nesmie mať viac ako :max znakov.',
    ],
    'max_digits' => 'Pole :attribute nesmie mať viac ako :max číslic.',
    'mimes' => 'Pole :attribute musí byť súbor typu: :values.',
    'mimetypes' => 'Pole :attribute musí byť súbor typu: :values.',
    'min' => [
        'array' => 'Pole :attribute musí mať aspoň :min položiek.',
        'file' => 'Pole :attribute musí mať aspoň :min kilobajtov.',
        'numeric' => 'Pole :attribute musí byť aspoň :min.',
        'string' => 'Pole :attribute musí mať aspoň :min znakov.',
    ],
    'min_digits' => 'Pole :attribute musí mať aspoň :min číslic.',
    'missing' => 'Pole :attribute musí chýbať.',
    'missing_if' => 'Pole :attribute musí chýbať, keď :other je :value.',
    'missing_unless' => 'Pole :attribute musí chýbať, pokiaľ :other nie je :value.',
    'missing_with' => 'Pole :attribute musí chýbať, keď je prítomné :values.',
    'missing_with_all' => 'Pole :attribute musí chýbať, keď sú prítomné :values.',
    'multiple_of' => 'Pole :attribute musí byť násobkom :value.',
    'not_in' => 'Vybraná hodnota pre :attribute je neplatná.',
    'not_regex' => 'Formát poľa :attribute je neplatný.',
    'numeric' => 'Pole :attribute musí byť číslo.',
    'password' => [
        'letters' => 'Pole :attribute musí obsahovať aspoň jedno písmeno.',
        'mixed' => 'Pole :attribute musí obsahovať aspoň jedno veľké a jedno malé písmeno.',
        'numbers' => 'Pole :attribute musí obsahovať aspoň jedno číslo.',
        'symbols' => 'Pole :attribute musí obsahovať aspoň jeden symbol.',
        'uncompromised' => 'Dané :attribute sa objavilo v úniku dát. Prosím, vyberte iné :attribute.',
    ],
    'present' => 'Pole :attribute musí byť prítomné.',
    'prohibited' => 'Pole :attribute je zakázané.',
    'prohibited_if' => 'Pole :attribute je zakázané, keď :other je :value.',
    'prohibited_unless' => 'Pole :attribute je zakázané, pokiaľ :other nie je v :values.',
    'prohibits' => 'Pole :attribute zakazuje prítomnosť :other.',
    'regex' => 'Formát poľa :attribute je neplatný.',
    'required' => 'Pole :attribute je povinné.',
    'required_array_keys' => 'Pole :attribute musí obsahovať položky pre: :values.',
    'required_if' => 'Pole :attribute je povinné, keď :other je :value.',
    'required_if_accepted' => 'Pole :attribute je povinné, keď je :other akceptované.',
    'required_unless' => 'Pole :attribute je povinné, pokiaľ :other nie je v :values.',
    'required_with' => 'Pole :attribute je povinné, keď je prítomné :values.',
    'required_with_all' => 'Pole :attribute je povinné, keď sú prítomné :values.',
    'required_without' => 'Pole :attribute je povinné, keď nie je prítomné :values.',
    'required_without_all' => 'Pole :attribute je povinné, keď nie je prítomné žiadne z :values.',
    'same' => 'Pole :attribute a :other sa musia zhodovať.',
    'size' => [
        'array' => 'Pole :attribute musí obsahovať :size položiek.',
        'file' => 'Pole :attribute musí mať :size kilobajtov.',
        'numeric' => 'Pole :attribute musí byť :size.',
        'string' => 'Pole :attribute musí mať :size znakov.',
    ],
    'starts_with' => 'Pole :attribute musí začínať jedným z nasledujúcich: :values.',
    'string' => 'Pole :attribute musí byť reťazec.',
    'timezone' => 'Pole :attribute musí byť platné časové pásmo.',
    'unique' => 'Pole :attribute už bolo použité.',
    'uploaded' => 'Nahrávanie poľa :attribute zlyhalo.',
    'uppercase' => 'Pole :attribute musí byť veľkými písmenami.',
    'url' => 'Pole :attribute musí byť platná URL.',
    'ulid' => 'Pole :attribute musí byť platný ULID.',
    'uuid' => 'Pole :attribute musí byť platný UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "rule.attribute" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'nickname' => 'prezývka',
        'ad_type' => 'typ inzerátu',
        'nationality' => 'národnosť',
        'age' => 'vek',
        'city' => 'mesto',
        'street' => 'ulica',
        'offer_type' => 'typ ponuky',
        'girl_selection' => 'výber dievčat',
        'experience' => 'skúsenosti',
        'phone' => 'telefón',
        'contact_methods' => 'spôsoby kontaktu',
        'hours' => 'hodiny',
        'practices' => 'praktiky',
        'description' => 'popis',
        'verification_photo' => 'overovacia fotka',
        'gallery_photos' => 'galéria fotiek',
        'video' => 'video',
        'height' => 'výška',
        'weight' => 'váha',
        'breast_size' => 'veľkosť pŕs',
        'eye_color' => 'farba očí',
        'hair_color' => 'farba vlasov',
        'tattoos' => 'tetovanie',
        'piercing' => 'piercing',
        'orientation' => 'orientácia',
        'name' => 'meno',
        'email' => 'email',
        'password' => 'heslo',
        'password_confirmation' => 'potvrdenie hesla',
    ],

]; 