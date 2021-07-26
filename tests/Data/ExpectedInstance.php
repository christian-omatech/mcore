<?php

return [
    'metadata' => [
        'className' => 'class-one',
        'caption' => 'Noticia',
        'id' => null,
        'key' => null,
        'allowedRelations' => [
            'class-two'
        ],
    ],
    'publication' => [
        'status' => 'pending',
        'startPublishingDate' => null,
        'endPublishingDate' => null
    ],
    'attributes' => [
        [
            'metadata' => [
                'id' => null,
                'key' => 'default-attribute'
            ],
            'component' => [
                'type' => 'string',
                'caption' => 'attribute.default-attribute.string',
            ],
            'values' => [
                [
                    'language' => 'es',
                    'rules' => [],
                    'configuration' => [],
                    'value' => null
                ], [
                    'language' => 'en',
                    'rules' => [],
                    'configuration' => [],
                    'value' => null
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
                        'caption' => 'attribute.sub-attribute.string',
                    ],
                    'values' => [
                        [
                            'language' => 'es',
                            'rules' => [],
                            'configuration' => [],
                            'value' => null
                        ], [
                            'language' => 'en',
                            'rules' => [],
                            'configuration' => [],
                            'value' => null
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
                'caption' => 'attribute',
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
                    'value' => null
                ], [
                    'language' => 'en',
                    'rules' => [
                        'required' => true
                    ],
                    'configuration' => [
                        'cols' => 10,
                        'rows' => 10
                    ],
                    'value' => null
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
                'caption' => 'attribute.specific-attribute.string',
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
                    'value' => null
                ], [
                    'language' => 'en',
                    'rules' => [
                        'required' => false
                    ],
                    'configuration' => [
                        'cols' => 20,
                        'rows' => 20
                    ],
                    'value' => null
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
                'caption' => 'attribute.all-languages-attribute.lookup',
            ],
            'values' => [
                [
                    'language' => '*',
                    'rules' => [],
                    'configuration' => [
                        'options' => [
                            'key1' => 'options.key1',
                            'key2' => 'Custom caption'
                        ]
                    ],
                    'value' => null
                ]
            ],
            'attributes' => []
        ]
    ]
];
