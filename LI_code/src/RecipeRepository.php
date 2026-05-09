<?php

require_once 'Database.php';
require_once 'RedisClient.php';

class RecipeRepository
{
    private PDO $pdo;
    private ?\Predis\Client $redis;

    public function __construct()
    {
        $this->pdo = Database::connect();
        $this->redis = RedisClient::connect(); // может быть null, если Redis не поднят
    }

    public function getAll(): array
    {
        // 🔥 Redis cache key
        $cacheKey = 'recipes:all';

        // 1. Пытаемся взять из Redis
        if ($this->redis) {
            $cached = $this->redis->get($cacheKey);
            if ($cached) {
                return json_decode($cached, true);
            }
        }

        // 2. Если нет кеша — идём в MySQL
        $stmt = $this->pdo->query('SELECT * FROM recipes ORDER BY id DESC');
        $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Сохраняем в Redis
        if ($this->redis) {
            $this->redis->set($cacheKey, json_encode($recipes));
            $this->redis->expire($cacheKey, 60); // 1 минута кеша
        }

        return $recipes;
    }

    public function create(array $data): void
    {
        $sql = 'INSERT INTO recipes
        (title, ingredients, instructions, category, prep_time, difficulty, created_at, author)
        VALUES
        (:title, :ingredients, :instructions, :category, :prep_time, :difficulty, :created_at, :author)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'title' => $data['title'],
            'ingredients' => $data['ingredients'],
            'instructions' => $data['instructions'],
            'category' => $data['category'],
            'prep_time' => $data['prep_time'],
            'difficulty' => $data['difficulty'],
            'created_at' => $data['created_at'],
            'author' => $data['author'],
        ]);

        $this->clearCache();
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM recipes WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $this->clearCache();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM recipes WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function update(int $id, array $data): void
    {
        $sql = 'UPDATE recipes
        SET title=:title,
            ingredients=:ingredients,
            instructions=:instructions,
            category=:category,
            prep_time=:prep_time,
            difficulty=:difficulty,
            created_at=:created_at,
            author=:author
        WHERE id=:id';

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'ingredients' => $data['ingredients'],
            'instructions' => $data['instructions'],
            'category' => $data['category'],
            'prep_time' => $data['prep_time'],
            'difficulty' => $data['difficulty'],
            'created_at' => $data['created_at'],
            'author' => $data['author'],
        ]);

        $this->clearCache();
    }

    private function clearCache(): void
    {
        if ($this->redis) {
            $this->redis->del('recipes:all');
        }
    }
}