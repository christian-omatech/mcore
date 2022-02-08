<?php declare(strict_types=1);

namespace Omatech\Mcore\Editora\Domain\Extraction;

use GraphQL\Error\SyntaxError;
use GraphQL\Language\AST\ArgumentNode;
use GraphQL\Language\AST\FieldNode;
use GraphQL\Language\AST\NodeList;
use GraphQL\Language\Parser as GraphQLParser;
use Omatech\Mcore\Shared\Utils\Utils;
use function Lambdish\Phunctional\reduce;
use function Lambdish\Phunctional\search;

final class Parser
{
    /**
     * @param string $query
     *
     * @return array<Query>
     *
     * @throws SyntaxError
     */
    public function parse(string $query): array
    {
        $graphQuery = GraphQLParser::parse(str_replace('()', '(limit: 0)', $query));
        return reduce(function (array $acc, FieldNode $node): array {
            $acc[] = $this->parseRootNode($node);
            return $acc;
        }, $graphQuery->definitions[0]->selectionSet->selections, []);
    }

    private function parseRootNode(FieldNode $node): Query
    {
        $params = $this->parseParams($node, 'class');
        return new Query([
            'attributes' => $this->parseAttributes($node),
            'params' => $params,
            'relations' => $this->parseRelations($node, [
                'languages' => $params['languages'],
                'preview' => $params['preview'],
            ]),
        ]);
    }

    /**
     * @param FieldNode $node
     * @param string $nodeType
     *
     * @return array<string, mixed>
     */
    private function parseParams(FieldNode $node, string $nodeType): array
    {
        $params = reduce(function (array $acc, ArgumentNode $argument): array {
            $acc[$argument->name->value] = $argument->value->value ??
                $this->parseArrayParams($argument->value->values);
            return $acc;
        }, $node->arguments, []);
        if ($node->name->value !== 'instances') {
            $params[$nodeType] = $node->name->value;
        }
        $params['class'] = Utils::getInstance()->slug($params['class'] ?? null);
        $params['key'] = Utils::getInstance()->slug($params['key'] ?? null);
        $params['preview'] = $params['preview'] ?? false;
        $params['limit'] = (int) ($params['limit'] ?? 0);
        $params['page'] = (int) ($params['page'] ?? 1);
        $params['languages'] = $this->parseLanguages($params['languages'] ?? []);
        return $params;
    }

    /**
     * @param NodeList $values
     *
     * @return array<string>
     */
    private function parseArrayParams(NodeList $values): array
    {
        return reduce(static function (array $acc, $value) {
            $acc[] = $value->value;
            return $acc;
        }, $values, []);
    }

    /**
     * @param string|array<string> $value
     *
     * @return array<string>
     */
    private function parseLanguages(string|array $value): array
    {
        if (is_string($value)) {
            return [$value];
        }
        return $value;
    }

    /**
     * @param FieldNode $node
     *
     * @return array<Attribute>
     */
    private function parseAttributes(FieldNode $node): array
    {
        return reduce(function (array $acc, FieldNode $node): array {
            if (! count($node->arguments)) {
                $acc[] = new Attribute(
                    Utils::getInstance()->slug($node->name->value),
                    $this->parseAttributes($node)
                );
            }
            return $acc;
        }, $node->selectionSet->selections ?? [], []);
    }

    /**
     * @param FieldNode $node
     * @param array<string, mixed> $params
     *
     * @return array<Query>
     */
    private function parseRelations(FieldNode $node, array $params = []): array
    {
        return reduce(function (array $acc, FieldNode $node) use ($params): array {
            if (count($node->arguments)) {
                $acc[] = new Query([
                    'attributes' => $this->parseAttributes($node),
                    'params' => $this->defaultRelationParams(array_merge(
                        $this->parseParams($node, 'key'),
                        $params
                    )),
                    'relations' => $this->parseRelations($node, $params),
                ]);
            }
            return $acc;
        }, $node->selectionSet->selections ?? [], []);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    private function defaultRelationParams(array $params): array
    {
        $params['type'] = $params['type'] ?? 'child';
        $params['type'] = search(static function (string $type) use ($params): bool {
            return $type === $params['type'];
        }, ['parent'], 'child');
        return $params;
    }
}
