<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Extraction;

use GraphQL\Language\AST\FieldNode;
use GraphQL\Language\Parser;
use Omatech\Mcore\Shared\Utils\Utils;
use function Lambdish\Phunctional\reduce;

final class QueryParser
{
    public function parse(string $query): Query
    {
        $graphQuery = Parser::parse($query);
        $rootNode = $graphQuery->definitions[0]->selectionSet->selections[0];
        return $this->parseNode($rootNode);
    }

    private function parseNode(FieldNode $node): Query
    {
        return new Query([
            'key' => Utils::getInstance()->slug($node->name->value),
            'attributes' => $this->parseAttributes($node),
            'params' => reduce(static function (array $acc, $argument) {
                $acc[$argument->name->value] = $argument->value->value;
                return $acc;
            }, $node->arguments, []),
            'relations' => $this->parseRelations($node),
        ]);
    }

    private function parseAttributes(FieldNode $node): array
    {
        return reduce(function (array $acc, FieldNode $node) {
            if (! count($node->arguments)) {
                $acc[] = new Attribute(
                    Utils::getInstance()->slug($node->name->value),
                    $this->parseAttributes($node)
                );
            }
            return $acc;
        }, $node->selectionSet->selections ?? [], []);
    }

    private function parseRelations(FieldNode $node): array
    {
        return reduce(function (array $acc, FieldNode $node) {
            if (count($node->arguments)) {
                $acc[] = new Relation(
                    Utils::getInstance()->slug($node->name->value),
                    reduce(static function (array $acc, $argument) {
                        $acc[$argument->name->value] = $argument->value->value;
                        return $acc;
                    }, $node->arguments, []),
                    $this->parseAttributes($node),
                    $this->parseRelations($node)
                );
            }
            return $acc;
        }, $node->selectionSet->selections ?? [], []);
    }
}
