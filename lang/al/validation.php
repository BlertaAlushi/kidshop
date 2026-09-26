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

    'accepted' => 'Fusha :attribute duhet të pranohet.',
    'accepted_if' => 'Fusha :attribute duhet të pranohet kur :other është :value.',
    'active_url' => 'Fusha :attribute duhet të jetë një URL e vlefshme.',
    'after' => 'Fusha :attribute duhet të jetë një datë pas :date.',
    'after_or_equal' => 'Fusha :attribute duhet të jetë një datë pas ose e barabartë me :date.',
    'alpha' => 'Fusha :attribute duhet të përmbajë vetëm shkronja.',
    'alpha_dash' => 'Fusha :attribute duhet të përmbajë vetëm shkronja, numra, viza dhe nënvija.',
    'alpha_num' => 'Fusha :attribute duhet të përmbajë vetëm shkronja dhe numra.',
    'any_of' => 'Fusha :attribute nuk është e vlefshme.',
    'array' => 'Fusha :attribute duhet të jetë një listë (array).',
    'ascii' => 'Fusha :attribute duhet të përmbajë vetëm karaktere alfanumerike dhe simbole njëbajtëshe.',
    'before' => 'Fusha :attribute duhet të jetë një datë para :date.',
    'before_or_equal' => 'Fusha :attribute duhet të jetë një datë para ose e barabartë me :date.',
    'between' => [
        'array' => 'Fusha :attribute duhet të ketë midis :min dhe :max elementësh.',
        'file' => 'Fusha :attribute duhet të jetë midis :min dhe :max kilobajtësh.',
        'numeric' => 'Fusha :attribute duhet të jetë midis :min dhe :max.',
        'string' => 'Fusha :attribute duhet të jetë midis :min dhe :max karakteresh.',
    ],
    'boolean' => 'Fusha :attribute duhet të jetë e vërtetë ose e gabuar.',
    'can' => 'Fusha :attribute përmban një vlerë të paautorizuar.',
    'confirmed' => 'Konfirmimi i fushës :attribute nuk përputhet.',
    'contains' => 'Fushës :attribute i mungon një vlerë e detyrueshme.',
    'current_password' => 'Fjalëkalimi është i pasaktë.',
    'date' => 'Fusha :attribute duhet të jetë një datë e vlefshme.',
    'date_equals' => 'Fusha :attribute duhet të jetë një datë e barabartë me :date.',
    'date_format' => 'Fusha :attribute duhet të përputhet me formatin :format.',
    'decimal' => 'Fusha :attribute duhet të ketë :decimal shifra dhjetore.',
    'declined' => 'Fusha :attribute duhet të refuzohet.',
    'declined_if' => 'Fusha :attribute duhet të refuzohet kur :other është :value.',
    'different' => 'Fusha :attribute dhe :other duhet të jenë të ndryshme.',
    'digits' => 'Fusha :attribute duhet të ketë :digits shifra.',
    'digits_between' => 'Fusha :attribute duhet të jetë midis :min dhe :max shifrash.',
    'dimensions' => 'Fusha :attribute ka përmasa jo të vlefshme të figurës.',
    'distinct' => 'Fusha :attribute ka një vlerë të përsëritur.',
    'doesnt_contain' => 'Fusha :attribute nuk duhet të përmbajë asnjë nga këto: :values.',
    'doesnt_end_with' => 'Fusha :attribute nuk duhet të mbarojë me asnjë nga këto: :values.',
    'doesnt_start_with' => 'Fusha :attribute nuk duhet të fillojë me asnjë nga këto: :values.',
    'email' => 'Fusha :attribute duhet të jetë një adresë email e vlefshme.',
    'encoding' => 'Fusha :attribute duhet të jetë e koduar në :encoding.',
    'ends_with' => 'Fusha :attribute duhet të mbarojë me njërën nga këto: :values.',
    'enum' => 'Vlera e zgjedhur për :attribute nuk është e vlefshme.',
    'exists' => 'Vlera e zgjedhur për :attribute nuk është e vlefshme.',
    'extensions' => 'Fusha :attribute duhet të ketë një nga këto shtesa: :values.',
    'file' => 'Fusha :attribute duhet të jetë një skedar.',
    'filled' => 'Fusha :attribute duhet të ketë një vlerë.',
    'gt' => [
        'array' => 'Fusha :attribute duhet të ketë më shumë se :value elementë.',
        'file' => 'Fusha :attribute duhet të jetë më e madhe se :value kilobajtë.',
        'numeric' => 'Fusha :attribute duhet të jetë më e madhe se :value.',
        'string' => 'Fusha :attribute duhet të jetë më e gjatë se :value karaktere.',
    ],
    'gte' => [
        'array' => 'Fusha :attribute duhet të ketë :value elementë ose më shumë.',
        'file' => 'Fusha :attribute duhet të jetë më e madhe ose e barabartë me :value kilobajtë.',
        'numeric' => 'Fusha :attribute duhet të jetë më e madhe ose e barabartë me :value.',
        'string' => 'Fusha :attribute duhet të jetë më e gjatë ose e barabartë me :value karaktere.',
    ],
    'hex_color' => 'Fusha :attribute duhet të jetë një ngjyrë e vlefshme heksadecimale.',
    'image' => 'Fusha :attribute duhet të jetë një figurë.',
    'in' => 'Vlera e zgjedhur për :attribute nuk është e vlefshme.',
    'in_array' => 'Fusha :attribute duhet të ekzistojë në :other.',
    'in_array_keys' => 'Fusha :attribute duhet të përmbajë të paktën një nga këto çelësa: :values.',
    'integer' => 'Fusha :attribute duhet të jetë një numër i plotë.',
    'ip' => 'Fusha :attribute duhet të jetë një adresë IP e vlefshme.',
    'ipv4' => 'Fusha :attribute duhet të jetë një adresë IPv4 e vlefshme.',
    'ipv6' => 'Fusha :attribute duhet të jetë një adresë IPv6 e vlefshme.',
    'json' => 'Fusha :attribute duhet të jetë një varg JSON i vlefshëm.',
    'list' => 'Fusha :attribute duhet të jetë një listë.',
    'lowercase' => 'Fusha :attribute duhet të jetë me shkronja të vogla.',
    'lt' => [
        'array' => 'Fusha :attribute duhet të ketë më pak se :value elementë.',
        'file' => 'Fusha :attribute duhet të jetë më e vogël se :value kilobajtë.',
        'numeric' => 'Fusha :attribute duhet të jetë më e vogël se :value.',
        'string' => 'Fusha :attribute duhet të jetë më e shkurtër se :value karaktere.',
    ],
    'lte' => [
        'array' => 'Fusha :attribute nuk duhet të ketë më shumë se :value elementë.',
        'file' => 'Fusha :attribute duhet të jetë më e vogël ose e barabartë me :value kilobajtë.',
        'numeric' => 'Fusha :attribute duhet të jetë më e vogël ose e barabartë me :value.',
        'string' => 'Fusha :attribute duhet të jetë më e shkurtër ose e barabartë me :value karaktere.',
    ],
    'mac_address' => 'Fusha :attribute duhet të jetë një adresë MAC e vlefshme.',
    'max' => [
        'array' => 'Fusha :attribute nuk duhet të ketë më shumë se :max elementë.',
        'file' => 'Fusha :attribute nuk duhet të jetë më e madhe se :max kilobajtë.',
        'numeric' => 'Fusha :attribute nuk duhet të jetë më e madhe se :max.',
        'string' => 'Fusha :attribute nuk duhet të jetë më e gjatë se :max karaktere.',
    ],
    'max_digits' => 'Fusha :attribute nuk duhet të ketë më shumë se :max shifra.',
    'mimes' => 'Fusha :attribute duhet të jetë një skedar i tipit: :values.',
    'mimetypes' => 'Fusha :attribute duhet të jetë një skedar i tipit: :values.',
    'min' => [
        'array' => 'Fusha :attribute duhet të ketë të paktën :min elementë.',
        'file' => 'Fusha :attribute duhet të jetë të paktën :min kilobajtë.',
        'numeric' => 'Fusha :attribute duhet të jetë të paktën :min.',
        'string' => 'Fusha :attribute duhet të jetë të paktën :min karaktere.',
    ],
    'min_digits' => 'Fusha :attribute duhet të ketë të paktën :min shifra.',
    'missing' => 'Fusha :attribute duhet të mungojë.',
    'missing_if' => 'Fusha :attribute duhet të mungojë kur :other është :value.',
    'missing_unless' => 'Fusha :attribute duhet të mungojë përveç kur :other është :value.',
    'missing_with' => 'Fusha :attribute duhet të mungojë kur :values është e pranishme.',
    'missing_with_all' => 'Fusha :attribute duhet të mungojë kur :values janë të pranishme.',
    'multiple_of' => 'Fusha :attribute duhet të jetë një shumëfish i :value.',
    'not_in' => 'Vlera e zgjedhur për :attribute nuk është e vlefshme.',
    'not_regex' => 'Formati i fushës :attribute nuk është i vlefshëm.',
    'numeric' => 'Fusha :attribute duhet të jetë një numër.',
    'password' => [
        'letters' => 'Fusha :attribute duhet të përmbajë të paktën një shkronjë.',
        'mixed' => 'Fusha :attribute duhet të përmbajë të paktën një shkronjë të madhe dhe një të vogël.',
        'numbers' => 'Fusha :attribute duhet të përmbajë të paktën një numër.',
        'symbols' => 'Fusha :attribute duhet të përmbajë të paktën një simbol.',
        'uncompromised' => 'Vlera e dhënë për :attribute është shfaqur në një rrjedhje të dhënash. Ju lutemi zgjidhni një :attribute tjetër.',
    ],
    'present' => 'Fusha :attribute duhet të jetë e pranishme.',
    'present_if' => 'Fusha :attribute duhet të jetë e pranishme kur :other është :value.',
    'present_unless' => 'Fusha :attribute duhet të jetë e pranishme përveç kur :other është :value.',
    'present_with' => 'Fusha :attribute duhet të jetë e pranishme kur :values është e pranishme.',
    'present_with_all' => 'Fusha :attribute duhet të jetë e pranishme kur :values janë të pranishme.',
    'prohibited' => 'Fusha :attribute është e ndaluar.',
    'prohibited_if' => 'Fusha :attribute është e ndaluar kur :other është :value.',
    'prohibited_if_accepted' => 'Fusha :attribute është e ndaluar kur :other është pranuar.',
    'prohibited_if_declined' => 'Fusha :attribute është e ndaluar kur :other është refuzuar.',
    'prohibited_unless' => 'Fusha :attribute është e ndaluar përveç kur :other është në :values.',
    'prohibits' => 'Fusha :attribute e ndalon :other të jetë e pranishme.',
    'regex' => 'Formati i fushës :attribute nuk është i vlefshëm.',
    'required' => 'Fusha :attribute është e detyrueshme.',
    'required_array_keys' => 'Fusha :attribute duhet të përmbajë hyrje për: :values.',
    'required_if' => 'Fusha :attribute është e detyrueshme kur :other është :value.',
    'required_if_accepted' => 'Fusha :attribute është e detyrueshme kur :other është pranuar.',
    'required_if_declined' => 'Fusha :attribute është e detyrueshme kur :other është refuzuar.',
    'required_unless' => 'Fusha :attribute është e detyrueshme përveç kur :other është në :values.',
    'required_with' => 'Fusha :attribute është e detyrueshme kur :values është e pranishme.',
    'required_with_all' => 'Fusha :attribute është e detyrueshme kur :values janë të pranishme.',
    'required_without' => 'Fusha :attribute është e detyrueshme kur :values nuk është e pranishme.',
    'required_without_all' => 'Fusha :attribute është e detyrueshme kur asnjë nga :values nuk është e pranishme.',
    'same' => 'Fusha :attribute duhet të përputhet me :other.',
    'size' => [
        'array' => 'Fusha :attribute duhet të përmbajë :size elementë.',
        'file' => 'Fusha :attribute duhet të jetë :size kilobajtë.',
        'numeric' => 'Fusha :attribute duhet të jetë :size.',
        'string' => 'Fusha :attribute duhet të jetë :size karaktere.',
    ],
    'starts_with' => 'Fusha :attribute duhet të fillojë me njërën nga këto: :values.',
    'string' => 'Fusha :attribute duhet të jetë tekst.',
    'timezone' => 'Fusha :attribute duhet të jetë një zonë orare e vlefshme.',
    'unique' => 'Vlera e fushës :attribute është përdorur tashmë.',
    'uploaded' => 'Ngarkimi i fushës :attribute dështoi.',
    'uppercase' => 'Fusha :attribute duhet të jetë me shkronja të mëdha.',
    'url' => 'Fusha :attribute duhet të jetë një URL e vlefshme.',
    'ulid' => 'Fusha :attribute duhet të jetë një ULID i vlefshëm.',
    'uuid' => 'Fusha :attribute duhet të jetë një UUID i vlefshëm.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
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
        'name' => 'emri',
        'email' => 'email',
        'phone' => 'numri i telefonit',
        'address' => 'adresa',
        'zip' => 'kodi postar',
        'city' => 'qyteti',
        'country' => 'shteti',
    ],

];
