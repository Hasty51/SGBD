<?php

require_once __DIR__ . '/../src/RecipeService.php';

$service = new RecipeService();

// 🔒 безопасное получение id
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 🚨 защита от некорректного запроса
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$service->deleteRecipe($id);

header('Location: index.php');
exit;