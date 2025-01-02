<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

return [
    'accepted' => 'El  :attribute debe ser aceptado.',
    'active_url' => 'El :attribute no es una URL válida.',
    'after' => 'El :attribute debe ser una fecha posterior a :date',
    'after_or_equal' => 'El :attribute debe ser una fecha despues o igual a :date.',
    'alpha' => 'El :attribute solo puede contener letras.',
    'alpha_dash' => 'El :attribute solamente puede contener letras, números y guiones.',
    'alpha_num' => 'El :attribute solamente puede contener letras y números.',
    'latin' => 'El :attribute solo puede contener letras del alfabeto latino básico.',
    'latin_dash_space' => 'El :attribute solo puede contener letras  del alfabeto latino básico, números, guiones y espacios.',
    'array' => 'El :attribute debe ser un arreglo.',
    'before' => 'El :attribute debe ser una fecha antes de :date.',
    'before_or_equal' => 'El :attribute debe ser una fecha antes o igual a :date.',
    'between' => [
        'numeric' => 'El :attribute debe estar entre :min y :max.',
        'file' => 'El :attribute debe estar entre :min y :max kilobytes.',
        'string' => 'El :attribute debe estar entre :min y :max caracteres.',
        'array' => 'El :attribute debe estar entre :min y :max items.',
    ],
    'boolean' => 'El campo :attribute debe ser verdadero o falso',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'current_password' => 'La contraseña que ha ingresado es incorrecta.',
    'date' => 'El :attribute no es una fecha valida.',
    'date_equals' => 'El :attribute debe ser una fecha igual a :date .',
    'date_format' => 'No coincide el formato :format de :attribute',
    'different' => 'El :attribute y:otro debe ser diferente.',
    'digits' => ':attribute deben ser digitos.',
    'digits_between' => 'El :attribute debe estar entre  min y : max digitos.',
    'dimensions' => 'la imagen :attribute tiene dimensiones incorrectas',
    'distinct' => 'El :attribute el campo tiene un valor duplicado',
    'email' => 'El :attribute debe ser una dirección de correo valida.',
    'ends_with' => 'El atributo: debe terminar con uno de los siguientes valores::.',
    'exists' => 'El : attribute seleccionado es invalido.',
    'file' => 'El :attribute debe ser un archivo.',
    'filled' => 'El campo :attribute es requerido.',
    'gt' => [
        'numeric' => 'El :attribute debe ser mayor de : value.',
        'file' => 'El :attribute debe ser mayor de : value kilobytes.',
        'string' => 'El :attribute debe ser mayor que  :value caracteres',
        'array' => 'El :attribute debe tener mas de: elementos de valor.',
    ],
    'gte' => [
        'numeric' => 'El :attribute debe ser mayor o igual que  :value',
        'file' => 'El :attribute debe ser mayor o igual que  :value kilobytes.',
        'string' => 'El :attribute debe ser mayor o igual que  :value caracteres.',
        'array' => 'El :attribute debe tener items o más',
    ],
    'image' => ':attribute debe ser una imagen.',
    'in' => 'El :attribute seleccionado es invalido.',
    'in_array' => 'El campo :attribute no existe en :other.',
    'integer' => 'El :attribute debe ser un entero.',
    'ip' => 'El :attribute debe ser una dirección ip valida.',
    'ipv4' => 'El :attribute debe ser una dirección IPv4 valida.',
    'ipv6' => 'El :attribute debe ser una dirección IPv6 valida.',
    'json' => 'El :attribute debe ser una cadena valida de JSON',
    'lt' => [
        'numeric' => 'El :attribute debe tener menos de :value.',
        'file' => 'El :attribute debe tener menos de :value kilobytes.',
        'string' => 'El :attribute debe tener menos de :value caracteres.',
        'array' => 'El :attribute debe tener menos de : value  elementos.',
    ],
    'lte' => [
        'numeric' => 'El :attribute debe ser igual o tener menos de :value.',
        'file' => 'El :attribute debe ser igual o tener menos de :value  kilobytes.',
        'string' => 'El :attribute debe ser igual o tener menos de :value  caracteres.',
        'array' => 'El :attribute debe ser igual o tener menos de :value  elementos.',
    ],
    'max' => [
        'numeric' => 'El :attribute no debe ser mas grande que :max.',
        'file' => 'El :attribute no debe ser mas grande que :max kilobytes.',
        'string' => 'El :attribute no debe ser mas grande que :max caracteres.',
        'array' => 'El :attribute no debe tener más de :max elementos.',
    ],
    'mimes' => 'El :attribute debe ser un fichero del tipo: :values',
    'mimetypes' => 'El :attribute debe ser un fichero del tipo: :values',
    'min' => [
        'numeric' => 'El :attribute debe ser al menos :min.',
        'file' => 'El :attribute debe ser al menos :min kylobytes.',
        'string' => 'El :attribute debe ser al menos :min caracteres.',
        'array' => 'El :attribute debe ser al menos :min elementos.',
    ],
    'not_in' => 'El :attribute seleccionado no és valido.',
    'not_regex' => 'El ::attribute  seleccionado no es válido.',
    'numeric' => 'El :attribute debe ser un número.',
    'password' => 'La contraseña es incorrecta',
    'present' => 'El campo del atributo :attribute debe estar presente.',
    'regex' => 'El formato del :attribute no es válido.',
    'required' => 'El campo :attribute es obligatorio.',
    'required_if' => 'El campo :attribute es requerido cuando :other tiene el valor :value.',
    'required_unless' => 'El campo :attribute es requerido salvo que :other esta en los valores :values.',
    'required_with' => 'El campo :attribute es requerido cuando :values esta presente.',
    'required_with_all' => 'El campo :attribute es requerido cuando :values esta presente.',
    'required_without' => 'El campo :attribute es requerido cuando :values no esta presente.',
    'required_without_all' => 'El campo :attribute es requerido cuando ninguno de los :values estan presentes.',
    'same' => 'El :attribute y :other deben coincidir.',
    'size' => [
        'numeric' => 'El :attribute debe ser :size.',
        'file' => 'El :attribute debe ser :size kilobytes.',
        'string' => 'El :attribute debe ser :size caracteres.',
        'array' => 'El :attribute debe ser :size elementos',
    ],
    'starts_with' => 'El :attribute debe comenzar con uno de los siguientes: :values.',
    'string' => 'El :attribute debe ser una cadena.',
    'timezone' => 'El :attribute debe ser una zona valida.',
    'unique' => 'El atributo :attribute ya ha sido usado.',
    'uploaded' => 'El :attribute no se pudo cargar.',
    'url' => 'El formato de :attribute es invalido.',
    'uuid' => 'El :attribute debe ser un UUID válido.',
    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],
    'reserved_word' => 'El :attribute contiene palabra reservada',
    'dont_allow_first_letter_number' => 'El campo \": input\" no puede tener la primera letra como un número',
    'exceeds_maximum_number' => 'El :attribute excede el maximo largo del modelo',
    'db_column' => 'El :attribute solo puede contener letras  del alfabeto latino básico, números, guiones y no puede empezar con un número.',
    'attributes' => [],
];
