<?php

namespace App\Core;

class View
{
    /**
     * Diretório onde estão guardadas as views.
     */
    private static string $viewsPath = __DIR__ . '/../../app/views';

    /**
     * Renderizar uma view com os dados recebidos.
     *
     * @param string $view  Nome da view, por exemplo: home/index
     * @param array  $data  Dados enviados pelo Controller
     */
    public static function render(string $view, array $data = []): void
    {
        // Construir o caminho completo da view.
        $viewPath = self::$viewsPath . '/' . $view . '.php';

        // Verificar se a view existe.
        if (!is_file($viewPath)) {
            http_response_code(500);
            throw new \RuntimeException(
                "View não encontrada: {$view}"
            );
        }

        // Extrair os dados para disponibilizá-los na view.
        extract($data, EXTR_SKIP);

        // Capturar o HTML gerado pela view.
        ob_start();

        try {
            require $viewPath;
            $content = ob_get_clean();
        } catch (\Throwable $exception) {
            ob_end_clean();
            throw $exception;
        }

        // Enviar o HTML ao navegador.
        echo $content;
    }
}
