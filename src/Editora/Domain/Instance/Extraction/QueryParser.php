<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Instance\Extraction;

use GraphQL\Language\AST\ArgumentNode;
use GraphQL\Language\AST\FieldNode;
use GraphQL\Language\Parser;
use Omatech\Mcore\Shared\Utils\Utils;
use function Lambdish\Phunctional\reduce;

final class QueryParser
{
    public function parse(string $query): array
    {
        $graphQuery = Parser::parse($query);
        return reduce(function (array $acc, FieldNode $node) {
            $acc[] = $this->parseNode($node);
            return $acc;
        }, $graphQuery->definitions[0]->selectionSet->selections, []);
    }

    private function parseNode(FieldNode $node): Query
    {
        $params = $this->parseParams($node);
        return new Query([
            'attributes' => $this->parseAttributes($node),
            'params' => $params,
            'relations' => $this->parseRelations($node, [
                'language' => $params['language'],
                'preview' => $params['preview'],
            ]),
        ]);
    }

    private function parseParams(FieldNode $node): array
    {
        $params = reduce(static function (array $acc, ArgumentNode $argument) {
            $acc[$argument->name->value] = $argument->value->value;
            return $acc;
        }, $node->arguments, []);

        $root = Utils::getInstance()->slug($node->name->value);
        $params['class'] = ($root === 'class') ? null : $root;

        $params['key'] = ($params['class'] === null) ? Utils::getInstance()->slug($params['key'] ?? '') ?? $root : null;
        $params['preview'] = (bool) ($params['preview'] ?? false);
        $params['limit'] = (int) ($params['limit'] ?? 0);
        $params['page'] = (int) ($params['page'] ?? 1);
        return $params;
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
                    'attributes' => $this->parseAttributes($node),
                    'params' => $this->parseParams($node) + $params,
                    'relations' => $this->parseRelations($node, $params),
                ]);
            }
            return $acc;
        }, $node->selectionSet->selections ?? [], []);
    }
}
