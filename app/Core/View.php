<?php
namespace App\Core;

use App\Repositories\CategoryRepository;
use League\Plates\Engine;

class View
{
    private Engine $engine;
    private CategoryRepository $categoryRepo; // Para o navbar

    public function __construct()
    {
        $this->engine = new Engine(dirname(__DIR__, 2) . '/views');
        $this->categoryRepo = new CategoryRepository();

        // Compartilha as categorias com todas as views (para o layout/navbar do site)
        $this->engine->addData(['allCategories' => $this->categoryRepo->findAll()]);
    }

    public function render(string $template, array $data = []): string
    {
        return $this->engine->render($template, $data);
    }
}