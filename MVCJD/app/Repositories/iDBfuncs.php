<?php

namespace Repositories;

declare(strict_types=1);

interface iDBFuncs {
    public function table($tablename): object;
    public function insert(array $values): int;
    public function get(): array; 
    public function getAll(): array;
    public function select(?array $fieldList = null): object;
    public function from($table): object;
    public function where(array $conditions): object;
    public function showQuery(): string;
    public function update(array $values): int;
    public function delete(): int;
    public function showValueBag(): array;
}
