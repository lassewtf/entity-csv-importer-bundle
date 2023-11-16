<?php

namespace Nano\EntityCsvImporterBundle\Service;

use Exception;

class ConfigSorter
{
    private function topologicalSort($graph): array
    {
        $sorted = [];
        $visited = [];
        $path = [];

        foreach ($graph as $node) {
            if (!in_array($node['entity'], $visited)) {
                $this->visit($node, $visited, $sorted, $graph, $path);
            }
        }

        return $sorted;
    }

    /**
     * @throws Exception
     */
    private function visit($node, &$visited, &$sorted, $graph, &$path): void
    {
        if (in_array($node['entity'], $path)) {
            throw new Exception('Zyklische Abhängigkeit entdeckt bei ' . $node['entity']);
        }

        if (in_array($node['entity'], $visited)) {
            return;
        }

        $path[] = $node['entity'];
        $visited[] = $node['entity'];

        $dependencies = $node['dependencies'] ?? [];
        foreach ($dependencies as $dep) {
            $depNode = array_filter($graph, function ($n) use ($dep) {
                return $n['entity'] === $dep;
            });
            if ($depNode) {
                $this->visit(array_values($depNode)[0], $visited, $sorted, $graph, $path);
            }
        }

        array_pop($path);
        $sorted[] = $node;
    }

    public function sort(array $config): array
    {
        try {
            return $this->topologicalSort($config);
        } catch (Exception $e) {
            dump($e->getMessage());
            die();
        }
    }
}