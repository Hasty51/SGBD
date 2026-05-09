<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/RecipeService.php';

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader);

$service = new RecipeService();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// 👉 GET = показать форму
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $recipe = $service->getRecipe($id);

    if (!$recipe) {
        header('Location: index.php');
        exit;
    }

    echo $twig->render('edit.twig', [
        'recipe' => $recipe
    ]);

    exit;
}

// 👉 POST = обновить
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

$service->updateRecipe($id, $data);

header('Location: view.php?id=' . $id);
exit;