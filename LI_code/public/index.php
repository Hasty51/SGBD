<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/RecipeService.php';

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// 📦 Twig setup
$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader);

// 🧠 Service layer
$service = new RecipeService();

// 📊 data
$recipes = $service->getRecipes();

// 🖥 render
echo $twig->render('index.twig', [
    'recipes' => $recipes
]);