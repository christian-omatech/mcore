<?php

namespace Tests\Editora\Data\Objects;

use Tests\Editora\Data\InstanceArrayBuilder;
use Tests\Editora\Data\InstanceFactory;

class LocationMother
{
    public static function get(int $instancesNumber = 1): array
    {
        $instances = [];
        for($i=1; $i <= $instancesNumber; $i++) {
            $instances[] = InstanceFactory::fill('Locations', static function (InstanceArrayBuilder $builder) use ($i) {
                return $builder
                    ->addMetadata('uuid', 'location-instance-'.$i)
                    ->addAttribute('country', 'string', [
                        ['uuid' => 'uuid', 'language' => 'es', 'value' => 'country-es-'.$i],
                        ['uuid' => 'uuid', 'language' => 'en', 'value' => 'country-en-'.$i]
                    ])
                    ->build();
            });
        }
        return $instances;
    }
}