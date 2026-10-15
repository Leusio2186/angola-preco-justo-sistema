<?php

namespace App\Core;

use PDO;
use InvalidArgumentException;

class QueryBuilder
{
    private PDO $pdo;
    private string $table;

    private array $columns = ['*'];
    private array $conditions = [];
    private array $bindings = [];
    private array $orders = [];
    private ?int $limitValue = null;

    public function __construct(PDO $pdo, string $table)
    {
        $this->pdo = $pdo;
        $this->table = $this->identifier($table);
    }

    /**
     * Validar e proteger identificadores SQL.
     * Os valores dos filtros são protegidos pelo PDO.
     */
    private function identifier(string $name): string
    {
        if (!preg_match(
            '/^[A-Za-z_][A-Za-z0-9_]*(\.[A-Za-z_][A-Za-z0-9_]*)?$/',
            $name
        )) {
            throw new InvalidArgumentException(
                "Identificador SQL inválido: {$name}"
            );
        }

        return implode(
            '.',
            array_map(
                fn($part) => "`{$part}`",
                explode('.', $name)
            )
        );
    }

    /**
     * Definir as colunas da consulta.
     */
    public function select(array $columns = ['*']): self
    {
        $this->columns = [];

        foreach ($columns as $column) {
            $this->columns[] = $column === '*'
                ? '*'
                : $this->identifier($column);
        }

        return $this;
    }

    /**
     * Adicionar um filtro WHERE.
     */
    public function where(
        string $column,
        string $operator,
        mixed $value
    ): self {
        $operator = strtoupper(trim($operator));

        $allowedOperators = [
            '=',
            '!=',
            '<>',
            '>',
            '<',
            '>=',
            '<=',
            'LIKE',
            'NOT LIKE'
        ];

        if (!in_array($operator, $allowedOperators, true)) {
            throw new InvalidArgumentException(
                'Operador WHERE não permitido.'
            );
        }

        if ($value === null) {
            if (!in_array($operator, ['=', '!=', '<>'], true)) {
                throw new InvalidArgumentException(
                    'Operador inválido para valores NULL.'
                );
            }

            $this->conditions[] = $this->identifier($column)
                . (in_array($operator, ['!=', '<>'], true)
                    ? ' IS NOT NULL'
                    : ' IS NULL');

            return $this;
        }

        $this->conditions[] = $this->identifier($column)
            . " {$operator} ?";

        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Ordenar os resultados.
     */
    public function orderBy(
        string $column,
        string $direction = 'ASC'
    ): self {
        $direction = strtoupper($direction);

        if (!in_array($direction, ['ASC', 'DESC'], true)) {
            throw new InvalidArgumentException(
                'Direção de ordenação inválida.'
            );
        }

        $this->orders[] = $this->identifier($column)
            . " {$direction}";

        return $this;
    }

    /**
     * Limitar a quantidade de resultados.
     */
    public function limit(int $limit): self
    {
        if ($limit < 0) {
            throw new InvalidArgumentException(
                'O limite não pode ser negativo.'
            );
        }

        $this->limitValue = $limit;

        return $this;
    }

    /**
     * Construir a cláusula WHERE.
     */
    private function buildWhere(): string
    {
        if (empty($this->conditions)) {
            return '';
        }

        return ' WHERE ' . implode(' AND ', $this->conditions);
    }

    /**
     * Executar uma consulta preparada.
     */
    private function execute(string $sql, array $bindings = []): \PDOStatement
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($bindings);

        return $statement;
    }

    /**
     * Buscar todos os registos.
     */
    public function get(): array
    {
        $sql = 'SELECT ' . implode(', ', $this->columns)
            . ' FROM ' . $this->table
            . $this->buildWhere();

        if (!empty($this->orders)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orders);
        }

        if ($this->limitValue !== null) {
            $sql .= ' LIMIT ' . $this->limitValue;
        }

        return $this->execute($sql, $this->bindings)
            ->fetchAll();
    }

    /**
     * Buscar apenas o primeiro registo.
     */
    public function first(): ?array
    {
        $this->limit(1);

        return $this->get()[0] ?? null;
    }

    /**
     * Contar os registos que correspondem aos filtros.
     */
    public function count(): int
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->table
            . $this->buildWhere();

        return (int) $this->execute(
            $sql,
            $this->bindings
        )->fetchColumn();
    }

    /**
     * Inserir um registo.
     * Devolve o ID gerado pela base de dados.
     */
    public function insert(array $data): string
    {
        if (empty($data)) {
            throw new InvalidArgumentException(
                'Os dados para inserir não podem estar vazios.'
            );
        }

        $columns = array_map(
            fn($column) => $this->identifier((string) $column),
            array_keys($data)
        );

        $placeholders = array_fill(0, count($data), '?');

        $sql = 'INSERT INTO ' . $this->table
            . ' (' . implode(', ', $columns) . ')'
            . ' VALUES (' . implode(', ', $placeholders) . ')';

        $this->execute($sql, array_values($data));

        return $this->pdo->lastInsertId();
    }

    /**
     * Atualizar registos.
     * Exige WHERE para evitar atualizar a tabela inteira.
     */
    public function update(array $data): int
    {
        if (empty($data)) {
            throw new InvalidArgumentException(
                'Os dados para atualizar não podem estar vazios.'
            );
        }

        if (empty($this->conditions)) {
            throw new InvalidArgumentException(
                'UPDATE exige pelo menos um filtro WHERE.'
            );
        }

        $sets = [];

        foreach ($data as $column => $value) {
            $sets[] = $this->identifier((string) $column) . ' = ?';
        }

        $sql = 'UPDATE ' . $this->table
            . ' SET ' . implode(', ', $sets)
            . $this->buildWhere();

        $bindings = array_merge(
            array_values($data),
            $this->bindings
        );

        return $this->execute($sql, $bindings)->rowCount();
    }

    /**
     * Eliminar registos.
     * Exige WHERE para evitar eliminar a tabela inteira.
     */
    public function delete(): int
    {
        if (empty($this->conditions)) {
            throw new InvalidArgumentException(
                'DELETE exige pelo menos um filtro WHERE.'
            );
        }

        $sql = 'DELETE FROM ' . $this->table
            . $this->buildWhere();

        return $this->execute($sql, $this->bindings)->rowCount();
    }
}
