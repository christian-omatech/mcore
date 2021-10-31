<?php declare(strict_types=1);

return [
    'class' => [
        'key' => 'class-one',
        'relations' => [
            [
                'key' => 'relation-key1',
                'classes' => [
                    'class-two',
                    'class-three',
                ],
            ], [
                'key' => 'relation-key2',
                'classes' => [
                    'class-four',
                    'class-five',
                ],
            ],
        ],
    ],
    'metadata' => [
        'key' => null,
        'uuid' => null,
        'publication' => [
            'status' => 'pending',
            'startPublishingDate' => null,
            'endPublishingDate' => null,
        ],
    ],
    'attributes' => [
        [
            'key' => 'default-attribute',
            'type' => 'string',
            'values' => [
                [
                    'language' => 'es',
                    'rules' => [],
                    'configuration' => [],
                    'value' => null,
                    'extraData' => [],
                    'uuid' => null,
                ], [
                    'language' => 'en',
                    'rules' => [],
                    'configuration' => [],
                    'value' => null,
                    'extraData' => [],
                    'uuid' => null,
                ],
            ],
            'attributes' => [
                [
                    'key' => 'sub-attribute',
                    'type' => 'string',
                    'values' => [
                        [
                            'language' => 'es',
                            'rules' => [],
                            'configuration' => [],
                            'value' => null,
                            'extraData' => [],
                            'uuid' => null,
                        ], [
                            'language' => 'en',
                            'rules' => [],
                            'configuration' => [],
                            'value' => null,
                            'extraData' => [],
                            'uuid' => null,
                        ],
                    ],
                    'attributes' => [],
                ],
            ],
        ], [
            'key' => 'global-attribute',
            'type' => 'textarea',
            'values' => [
                [
                    'language' => 'es',
                    'rules' => [
                        'required' => true,
                    ],
                    'configuration' => [
                        'cols' => 10,
                        'rows' => 10,
                    ],
                    'value' => null,
                    'extraData' => [],
                    'uuid' => null,
                ], [
                    'language' => 'en',
                    'rules' => [
                        'required' => true,
                    ],
                    'configuration' => [
                        'cols' => 10,
                        'rows' => 10,
                    ],
                    'value' => null,
                    'extraData' => [],
                    'uuid' => null,
                ],
            ],
            'attributes' => [],
        ], [
            'key' => 'specific-attribute',
            'type' => 'string',
            'values' => [
                [
                    'language' => 'es',
                    'rules' => [
                        'required' => true,
                    ],
                    'configuration' => [
                        'cols' => 10,
                        'rows' => 10,
                    ],
                    'value' => null,
                    'extraData' => [],
                    'uuid' => null,
                ], [
                    'language' => 'en',
                    'rules' => [
                        'required' => false,
                    ],
                    'configuration' => [
                        'cols' => 20,
                        'rows' => 20,
                    ],
                    'value' => null,
                    'extraData' => [],
                    'uuid' => null,
                ], [
                    'language' => '+',
                    'rules' => [
                        'required' => true,
                    ],
                    'configuration' => [
                        'cols' => 30,
                        'rows' => 30,
                    ],
                    'value' => null,
                    'extraData' => [],
                    'uuid' => null,
                ],
            ],
            'attributes' => [],
        ], [
            'key' => 'all-languages-attribute',
            'type' => 'lookup',
            'values' => [
                [
                    'language' => '*',
                    'rules' => [
                        'required' => false,
                    ],
                    'configuration' => [
                        'options' => [
                            'key1',
                            'key2',
                        ],
                    ],
                    'value' => null,
                    'extraData' => [],
                    'uuid' => null,
                ],
            ],
            'attributes' => [],
        ],
    ],
    'relations' => [],
];
