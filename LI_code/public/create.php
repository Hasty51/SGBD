<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/RecipeService.php';

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader);

$service = new RecipeService();

// 👉 GET = показать форму
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo $twig->render('create.twig');
    exit;
}

// 👉 POST = сохранить
$data = [
    'title' => $_POST['title'] ?? '',
    'ingredients' => $_POST['ingredients'] ?? '',
    'instructions' => $_POST['instructions'] ?? '',
    'category' => $_POST['category'] ?? '',
    'prep_time' => (int)($_POST['prep_time'] ?? 0),
    'difficulty' => $_POST['difficulty'] ?? 'easy',
    'created_at' => date('Y-m-d'),
    'author' => $_POST['author'] ?? 'anonymous',
];

$service->createRecipe($data);

header('Location: index.php');
exit;