<?php declare(strict_types=1);

namespace Tests\Editora\Data;

use Omatech\Mcore\Editora\Domain\Instance\Contracts\InstanceCacheInterface;
use Omatech\Mcore\Editora\Domain\Instance\Instance;
use Omatech\Mcore\Editora\Domain\Instance\InstanceBuilder as InstanceBuilderReal;
use Omatech\Mcore\Shared\Utils\Utils;

class InstanceBuilder
{
    private array $languages = ['es', 'en'];
    private string $className = 'VideoGames';
    private array $structure = [];
    private InstanceCacheInterface $instanceCache;

    public function __construct(InstanceCacheInterface $instanceCache)
    {
        $this->instanceCache = $instanceCache;
        $this->structure = (include __DIR__ . '/structure.php')['classes'][$this->className];
    }

    public function build(): Instance
    {
        return (new InstanceBuilderReal($this->instanceCache))
            ->setLanguages($this->languages)
            ->setClassName($this->className)
            ->setStructure($this->structure)
            ->build();
    }

    public function setLanguages(array $languages): InstanceBuilder
    {
        $this->languages = array_fill_keys($languages, []);
        return $this;
    }

    public function setStructure(array $structure): InstanceBuilder
    {
        $this->structure = $structure;
        return $this;
    }

    public function setClassName(string $className): InstanceBuilder
    {
        $this->className = Utils::getInstance()->slug($className);
        return $this;
    }
}