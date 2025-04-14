<?php

namespace Framework\Kernel;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Framework\Http\Response;
use App\IndexController;
use App\ResultsController;
class Kernel implements KernelInterface
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $path = $request->getUri()->getPath();

        if ($path === '/') {
            $controller = new IndexController();
            return $controller->handle($request);
        } elseif ($path === '/results') {
            $controller = new ResultsController();
            return $controller->handle($request);
        }

        $html = '<html><body>';
        $html .= '<h1>404 Not Found</h1>';
        $html .= '<p>De pagina ' . htmlspecialchars($path) . ' bestaat niet.</p>';
        $html .= '</body></html>';

        return new Response($html, 404, '1.1', ['Content-Type' => 'text/html']);
    }
}

