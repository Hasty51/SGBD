<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/RecipeService.php';

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// Twig setup
$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader);

// Service
$service = new RecipeService();

// 🔒 безопасное получение id
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 🚨 защита от неправильного запроса
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// 📦 data
$recipe = $service->getRecipe($id);

// если рецепт не найден
if (!$recipe) {
    header('Location: index.php');
    exit;
}

$views = $service->getViews($id);

// 🖥 render
echo $twig->render('view.twig', [
    'recipe' => $recipe,
    'views' => $views ?? 0
]);