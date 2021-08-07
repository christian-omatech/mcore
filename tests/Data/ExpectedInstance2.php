<?php

return [
    'class' => [
        'key' => 'class-one',
        'relations' => [
            [
                'key' => 'relation-key1',
                'classes' => [
                    'class-two',
                    'class-three',
                ]
            ], [
                'key' => 'relation-key2',
                'classes' => [
                    'class-four',
                    'class-five',
                ]
            ]
        ]
    ],
    'metadata' => [
        'key' => 'soy-la-key-de-la-instancia',
        'id' => 1,
        'publication' => [
            'status' => 'in-revision',
            'startPublishingDate' => '08/03/1989 09:00:00',
            'endPublishingDate' => '27/07/2021 14:30:00'
        ],
    ],
    'attributes' => [
        [
            'metadata' => [
                'id' => null,
                'key' => 'default-attribute'
            ],
            'component' => [
                'type' => 'string',
            ],
            'values' => [
                [
                    'language' => 'es',
                    'rules' => [],
                    'configuration' => [],
                    'value' => 'hola'
                ], [
                    'language' => 'en',
                    'rules' => [],
                    'configuration' => [],
                    'value' => 'adios'
                ]
            ],
            'attributes' => [
                [
                    'metadata' => [
                        'id' => null,
                        'key' => 'sub-attribute'
                    ],
                    'component' => [
                        'type' => 'string',
                    ],
                    'values' => [
                        [
                            'language' => 'es',
                            'rules' => [],
                            'configuration' => [],
                            'value' => 'hola'
                        ], [
                            'language' => 'en',
                            'rules' => [],
                            'configuration' => [],
                            'value' => 'adios'
                        ]
                    ],
                    'attributes' => []
                ]
            ],
        ], [
            'metadata' => [
                'id' => null,
                'key' => 'global-attribute'
            ],
            'component' => [
                'type' => 'textarea',
            ],
            'values' => [
                [
                    'language' => 'es',
                    'rules' => [
                        'required' => true
                    ],
                    'configuration' => [
                        'cols' => 10,
                        'rows' => 10
                    ],
                    'value' => 'hola'
                ], [
                    'language' => 'en',
                    'rules' => [
                        'required' => true
                    ],
                    'configuration' => [
                        'cols' => 10,
                        'rows' => 10
                    ],
                    'value' => 'adios'
                ],
            ],
            'attributes' => []
        ], [
            'metadata' => [
                'id' => null,
                'key' => 'specific-attribute'
            ],
            'component' => [
                'type' => 'string',
            ],
            'values' => [
                [
                    'language' => 'es',
                    'rules' => [
                        'required' => true
                    ],
                    'configuration' => [
                        'cols' => 10,
                        'rows' => 10
                    ],
                    'value' => 'hola'
                ], [
                    'language' => 'en',
                    'rules' => [
                        'required' => false
                    ],
                    'configuration' => [
                        'cols' => 20,
                        'rows' => 20
                    ],
                    'value' => 'adios'
                ], [
                    'language' => '+',
                    'rules' => [
                        'required' => true
                    ],
                    'configuration' => [
                        'cols' => 30,
                        'rows' => 30
                    ],
                    'value' => 'default'
                ],
            ],
            'attributes' => []
        ], [
            'metadata' => [
                'id' => null,
                'key' => 'all-languages-attribute'
            ],
            'component' => [
                'type' => 'lookup',
            ],
            'values' => [
                [
                    'language' => '*',
                    'rules' => [],
                    'configuration' => [
                        'options' => [
                            'key1',
                            'key2'
                        ]
                    ],
                    'value' => 'hola'
                ]
            ],
            'attributes' => []
        ]
    ]
];
