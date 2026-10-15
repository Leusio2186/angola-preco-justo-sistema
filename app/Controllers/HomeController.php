<?php

namespace App\Controllers;


use App\Core\View;
use App\Core\DB;

class HomeController
{
    public function index(): void
    {
        // Simulação de dados que futuramente virão da base de dados.
        $products = [
            ['name' => 'Arroz', 'price' => 8500],
            ['name' => 'Açúcar', 'price' => 4200],
            ['name' => 'Óleo alimentar', 'price' => 3000],
        ];

        $products3 = DB::table('users')->get();

        // Enviar os dados para a view.
        View::render('home/index', [
            'title' => 'PreçoHuambo',
            'message' => 'Compara os preços dos produtos.',
            'products' => $products,
            'products3' => $products3,
        ]);
    }

    public function login(): void
    {
        dd("ola login");
    }

    public function ola(): void
    {
        echo 'Página de contacto';
    }
}
