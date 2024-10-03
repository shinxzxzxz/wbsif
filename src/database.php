<?php

namespace App;

class Database
{
    private string $hostname;
    private string $database;
    private string $username;
    private string $password;
    protected \mysqli|bool|null $instance;

    public function __construct(
        ?string $hostname = null,
        ?string $database = null,
        ?string $username = null,
        ?string $password = null,
    ) {
        $this->hostname = $hostname ?? $_ENV['DB_HOST'] ?? 'localhost';
        $this->database = $database ?? $_ENV['DB_DATABASE'] ?? '';
        $this->username = $username ?? $_ENV['DB_USERNAME'] ?? 'root';
        $this->password = $password ?? $_ENV['DB_PASSWORD'] ?? '';
        
        if (!extension_loaded('mysqli')) {
            die("Mysqli extension not loaded.");
        }

        if (empty($this->database)) {
            die("No database selected.");
        }
        
        $this->instance = mysqli_connect(
            hostname: $this->hostname,
            username: $this->username,
            password: $this->password,
            database: $this->database,
        );

        if ($this->instance === false) {
            die("Database.start: Unable to start or initialize connection: " . mysqli_connect_error());
        }
    }

    public function query(string $sql): DatabaseResult|null
    {
        $result = mysqli_query(mysql: $this->instance, query: $sql);
        if ($result === false) {
            return null;
        }
        return new DatabaseResult(result: $result);
    }
}

class DatabaseResult
{
    private \mysqli_result $result;

    public function __construct(\mysqli_result $result)
    {
        $this->result = $result;
    }

    public function fields(): array
    {
        return $this->result->fetch_fields();
    }

    public function assoc(): ?array
    {
        return $this->result->fetch_assoc();
    }

    public function row(): ?array
    {
        return $this->result->fetch_row();
    }

    public function object(): ?object
    {
        return $this->result->fetch_object();
    }

    public function count(): int
    {
        return $this->result->num_rows;
    }

    public function all()
    {
        return new class($this) {
            private $instance;

            public function __construct($instance)
            {
                $this->instance = $instance;
            }

            public function assoc(): array
            {
                $rows = [];
                while ($row = $this->instance->assoc()) {
                    $rows[] = $row;
                }
                return $rows;
            }

            public function objects(): array
            {
                $rows = [];
                while ($row = $this->instance->object()) {
                    $rows[] = $row;
                }
                return $rows;
            }

            public function rows(): array
            {
                $rows = [];
                while ($row = $this->instance->row()) {
                    $rows[] = $row;
                }
                return $rows;
            }
        };
    }

    public function free(): void
    {
        $this->result->free();
    }

    public function seek(int $offset): bool
    {
        return $this->result->data_seek($offset);
    }

    public function exists(): bool
    {
        return $this->count() > 0;
    }

    public function for_each(callable $callback): void
    {
        while ($row = $this->assoc()) {
            $callback($row);
        }
    }

    public function paginate(int $limit, int $offset = 0): array
    {
        $this->seek(offset: $offset);
        $rows = [];
        for ($i = 0; $i < $limit && ($row = $this->assoc()); $i++) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function get_column(string $column_name): array
    {
        $column = [];
        while ($row = $this->assoc()) {
            if (array_key_exists($column_name, $row)) {
                $column[] = $row[$column_name];
            }
        }
        return $column;
    }
    public function map(callable $callback): array
    {
        $mapped = [];
        while ($row = $this->assoc()) {
            $mapped[] = $callback($row);
        }
        return $mapped;
    }

    public function reduce(callable $callback, $initial = null)
    {
        $accumulator = $initial;
        while ($row = $this->assoc()) {
            $accumulator = $callback($accumulator, $row);
        }
        return $accumulator;
    }
}