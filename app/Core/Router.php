<?php

namespace App\Core;

class Router
{
    /**
     * Armazena todas as rotas registadas.
     *
     * Estrutura:
     * [
     *     'GET' => [
     *         '/' => [HomeController::class, 'index']
     *     ],
     *     'POST' => []
     * ]
     */
    private array $routes = [];

    /**
     * Registar uma rota GET.
     */
    public function get(string $path, array $action): void
    {
        $this->routes['GET'][$this->normalizePath($path)] = $action;
    }

    /**
     * Registar uma rota POST.
     */
    public function post(string $path, array $action): void
    {
        $this->routes['POST'][$this->normalizePath($path)] = $action;
    }

    /**
     * Normalizar caminhos para evitar diferenças
     * entre /, /home e /home/.
     */
    private function normalizePath(string $path): string
    {
        return '/' . trim($path, '/');
    }

    /**
     * Encontrar e executar a rota correspondente.
     */

    public function dispatch(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);

        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

        // Prefixo da aplicação no XAMPP.
        $basePath = '/angola-preco-justo-sistema/public';

        // Remover o prefixo apenas quando corresponde ao início do caminho.
        if (
            $path === $basePath ||
            str_starts_with($path, $basePath . '/')
        ) {
            $path = substr($path, strlen($basePath));
        }

        // Normalizar o caminho.
        $path = '/' . trim($path, '/');

        // DEBUG: mostrar o caminho que o Router está a procurar.
        // Remove estas linhas depois do teste.
        // var_dump($method, $path, array_keys($this->routes[$method] ?? []));
        // exit;

        $action = $this->routes[$method][$path] ?? null;

        if ($action === null) {
            http_response_code(404);
            echo 'Página não encontrada: ' . htmlspecialchars($path);
            return;
        }

        [$controller, $controllerMethod] = $action;

        $instance = new $controller();

        $instance->$controllerMethod();
    }
}
