<?php declare(strict_types=1);

return [
    'classes' => [
        'News' => [
            'attributes' => [
                'title' => null,
                'description' => null,
            ],
            'relations' => [
                'NewsPhotos' => [
                    'Photos',
                ],
            ],
        ],
        'Articles' => [
            'attributes' => [
                'title' => null,
                'author' => null,
                'page' => null,
            ],
        ],
        'Pictures' => [
            'attributes' => [
                'url' => null,
            ],
        ],
        'Photos' => [
            'attributes' => [
                'url' => null,
            ],
            'relations' => [
                'PhotosLocations' => [
                    'Locations',
                    'Coordinates'
                ],
            ],
        ],
        'Locations' => [
            'attributes' => [
                'country' => null,
            ],
        ],        
        'Coordinates' => [
            'attributes' => [
                'latitude' => null,
                'longitude' => null,
            ],
        ],
        'Books' => [
            'attributes' => [
                'title' => null,
                'isbn' => [
                    'values' => [
                        'languages' => [
                            '+' => [],
                        ],
                    ],
                ],
                'synopsis' => [
                    'values' => [
                        'languages' => [
                            '+' => [],
                        ],
                    ],
                ],
                'picture' => [
                    'attributes' => [
                        'alt' => null,
                    ],
                ],
                'price' => [
                    'values' => [
                        'languages' => [
                            '*' => [],
                        ],
                    ],
                ],
            ],
            'relations' => [
                'Articles' => [
                    'Articles',
                ],
                'Photos' => [
                    'Photos',
                    'Pictures',
                ],
            ],
        ],
    ],
];
