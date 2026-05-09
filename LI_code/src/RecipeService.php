<?php

require_once 'RecipeRepository.php';
require_once 'RedisClient.php';

class RecipeService
{
    private RecipeRepository $repository;
    private ?\Predis\Client $redis;
    public function __construct()
    {
        $this->repository = new RecipeRepository();
        $this->redis = RedisClient::connect(); // может быть null
    }

    public function getRecipes(): array
    {
        return $this->repository->getAll();
    }

    public function createRecipe(array $data): void
    {
        $this->repository->create($data);
    }

    public function deleteRecipe(int $id): void
    {
        $this->repository->delete($id);

        $this->clearViewsCache($id);
    }

    public function updateRecipe(int $id, array $data): void
    {
        $this->repository->update($id, $data);

        $this->clearViewsCache($id);
    }

    public function getRecipe(int $id): ?array
    {
        // 📊 safely increment views
        if ($this->redis) {
            $this->redis->incr($this->viewsKey($id));
        }

        return $this->repository->find($id);
    }

    public function getViews(int $id): int
    {
        if (!$this->redis) {
            return 0;
        }

        return (int) $this->redis->get($this->viewsKey($id));
    }

    private function viewsKey(int $id): string
    {
        return "recipe:$id:views";
    }

    private function clearViewsCache(int $id): void
    {
        if ($this->redis) {
            $this->redis->del($this->viewsKey($id));
        }
    }
}