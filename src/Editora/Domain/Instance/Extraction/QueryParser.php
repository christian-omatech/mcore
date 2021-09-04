<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Extraction;

use GraphQL\Language\AST\FieldNode;
use GraphQL\Language\Parser;
use Omatech\Mcore\Shared\Utils\Utils;
use function Lambdish\Phunctional\reduce;

final class QueryParser
{
    public function parse(string $query): Query|array
    {
        $graphQuery = Parser::parse($query);
        return reduce(function (array $acc, FieldNode $node) {
            $acc[] = $this->parseNode($node);
            return $acc;
        }, $graphQuery->definitions[0]->selectionSet->selections, []);
    }

    private function parseNode(FieldNode $node): Query
    {
        $params = reduce(static function (array $acc, $argument) {
            $acc[$argument->name->value] = $argument->value->value;
            return $acc;
        }, $node->arguments, []);
        return new Query([
            'key' => Utils::getInstance()->slug($node->name->value),
            'attributes' => $this->parseAttributes($node),
            'params' => $params,
            'relations' => $this->parseRelations($node, [
                'language' => $params['language'],
                'preview' => $params['preview'],
            ]),
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

    private function parseRelations(FieldNode $node, array $params = []): array
    {
        return reduce(function (array $acc, FieldNode $node) use ($params) {
            if (count($node->arguments)) {
                $acc[] = new Query([
                    'key' => Utils::getInstance()->slug($node->name->value),
                    'attributes' => $this->parseAttributes($node),
                    'params' => reduce(static function (array $acc, $argument) {
                        $acc[$argument->name->value] = $argument->value->value;
                        return $acc;
                    }, $node->arguments, []) + $params,
                    'relations' => $this->parseRelations($node, $params),
                ]);
            }
            return $acc;
        }, $node->selectionSet->selections ?? [], []);
    }
}
