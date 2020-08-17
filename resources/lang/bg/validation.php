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

    'accepted' => ':attribute трябва да бъде приет.',
    'active_url' => ':attribute не в валиден URL.',
    'after' => ':attribute трябва да е дата след :date.',
    'after_or_equal' => ':attribute трябва да е дата след или същата като :date.',
    'alpha' => ':attribute може да съдържа само букви.',
    'alpha_dash' => ':attribute може да съдържа само букви, числа, тирета и долни черти.',
    'alpha_num' => ':attribute може да съдържа само букви и числа.',
    'array' => ':attribute трябва да е масив.',
    'before' => ':attribute трябва да е дата преди :date.',
    'before_or_equal' => ':attribute трябва да е дата преди или същата като :date.',
    'between' => [
        'numeric' => ':attribute трябва да е между :min и :max.',
        'file' => ':attribute трябва да е между :min и :max килобайта.',
        'string' => ':attribute трябва да е между :min и :max символа.',
        'array' => ':attribute трябва да е между :min и :max неща.',
    ],
    'boolean' => ':attribute полето трябва да е или истина или лъжа.',
    'confirmed' => ':attribute потвърждението не съвпада.',
    'date' => ':attribute не е валидна дата.',
    'date_equals' => ':attribute трябва да е същата дата като :date.',
    'date_format' => ':attribute не съвпада с форматът :format.',
    'different' => ':attribute и :other трябва да са различни.',
    'digits' => ':attribute трябва да е :digits числа.',
    'digits_between' => ':attribute трябва да е между :min и :max числа.',
    'dimensions' => ':attribute има невалидни размери за изображение.',
    'distinct' => ':attribute полето има повтаряща се стойност.',
    'email' => ':attribute трябва де е валиден имейл.',
    'ends_with' => ':attribute трябва да завършва с една от следните стойности: :values.',
    'exists' => 'Избраното :attribute не е валидно.',
    'file' => ':attribute трябва да е файл.',
    'filled' => ':attribute полето трябва да има стойност.',
    'gt' => [
        'numeric' => ':attribute трябва да е по-голямо от :value.',
        'file' => ':attribute трябва да е повече от :value килобайта.',
        'string' => ':attribute трябва да е повече от :value символа.',
        'array' => ':attribute трябва да има повече от :value неща.',
    ],
    'gte' => [
        'numeric' => ':attribute трябва да е по-голямо или равно на :value.',
        'file' => ':attribute трябва да е поне :value килобайта.',
        'string' => ':attribute трябва да е поне :value символа.',
        'array' => ':attribute трябва да има :value неща или повече.',
    ],
    'image' => ':attribute трябва да е изображение.',
    'in' => ':attribute е невалидно.',
    'in_array' => ':attribute не съществува в :other.',
    'integer' => ':attribute трябва да е число.',
    'ip' => ':attribute трябва да е валиден IP адрес.',
    'ipv4' => ':attribute трябва да е валиден IPv4 адрес.',
    'ipv6' => ':attribute трябва да е валиден IPv6 адрес.',
    'json' => ':attribute трябва да е валиден JSON низ.',
    'lt' => [
        'numeric' => ':attribute трябва да е по-малко от :value.',
        'file' => ':attribute трябва да е по-малко от :value килобайта.',
        'string' => ':attribute трябва да е по-малко от :value символа.',
        'array' => ':attribute трябва да има по-малко от :value неща.',
    ],
    'lte' => [
        'numeric' => ':attribute трябва да е по-малко или равно на :value.',
        'file' => ':attribute трябва да е поне :value килобайта.',
        'string' => ':attribute трябва да е поне :value символа.',
        'array' => ':attribute не трябва да има повече от :value неща.',
    ],
    'max' => [
        'numeric' => ':attribute не може да е по-голямо от :max.',
        'file' => ':attribute не може да е повече от :max килобайта.',
        'string' => ':attribute не може да е повече от :max символа.',
        'array' => ':attribute не може да има повече от :max неща.',
    ],
    'mimes' => ':attribute трябва да е файл от тип: :values.',
    'mimetypes' => ':attribute трябва да е файл от тип:: :values.',
    'min' => [
        'numeric' => ':attribute трябва да е поне :min.',
        'file' => ':attribute трябва да е поне :min килобайта.',
        'string' => ':attribute трябва да е поне :min символа.',
        'array' => ':attribute трябва да има поне :min неща.',
    ],
    'not_in' => ':attribute е невалидно.',
    'not_regex' => ':attribute е невалиден формат.',
    'numeric' => ':attribute трябва да е число.',
    'password' => 'Паролата е невалидна.',
    'present' => ':attribute полето трябва да е представено.',
    'regex' => ':attribute е невалиден формат.',
    'required' => ':attribute полето е задължително.',
    'required_if' => ':attribute полето в задължително когато :other е :value.',
    'required_unless' => ':attribute полето е задължително освен :other е в :values.',
    'required_with' => ':attribute полето е задължително когато :values е предоставено.',
    'required_with_all' => ':attribute полето е задължително когато :values са предоставени.',
    'required_without' => ':attribute полето е задължително когато :values не са предоставени.',
    'required_without_all' => ':attribute полето е задължително когато никое от :values са предоставени.',
    'same' => ':attribute и :other трябва да съвпадат.',
    'size' => [
        'numeric' => ':attribute трябва да е :size.',
        'file' => ':attribute трябва да е :size килобайта.',
        'string' => ':attribute трябва да е :size сивмола.',
        'array' => ':attribute трябва да съдържа :size неща.',
    ],
    'starts_with' => ':attribute трябва да започва с едно от следните: :values.',
    'string' => ':attribute трябва да е низ.',
    'timezone' => ':attribute трябва да е валидна зона.',
    'unique' => ':attribute вече е заето.',
    'uploaded' => ':attribute не успя да се качи.',
    'url' => ':attribute е невалиден формат.',
    'uuid' => ':attribute трябва да е валиден UUID.',

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

    'attributes' => [],

];
