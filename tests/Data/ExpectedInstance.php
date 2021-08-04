<?php

return [
    'class' => [
        'key' => 'class-one',
        'relationGroups' => [
            [
                'key' => 'relation-key1',
                'relations' => [
                    'class-two',
                    'class-three',
                ]
            ], [
                'key' => 'relation-key2',
                'relations' => [
                    'class-four',
                    'class-five',
                ]
            ]
        ]
    ],
    'metadata' => [
        'key' => null,
        'id' => null,
        'publication' => [
            'status' => 'pending',
            'startPublishingDate' => null,
            'endPublishingDate' => null
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
                    'value' => null
                ]
            ],
            'attributes' => []
        ]
    ]
];
