<?php

namespace App;

use Framework\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ResultsController
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {

        $answers = $request->getParsedBody()['answers'] ?? [];

        $html = '<html><body>';
        $html .= '<h1>Resultaten</h1>';
        $html .= '<h2>Jouw Antwoorden</h2>';
        $html .= '<ul>';
        foreach ($answers as $statementId => $answer) {
            $html .= '<li>Stelling ID ' . htmlspecialchars($statementId) . ': ' . htmlspecialchars($answer) . '</li>';
        }
        $html .= '</ul>';
        $html .= '<a href="/">Terug naar het formulier</a>';
        $html .= '</body></html>';

        return new Response($html, 200, "1.1", ['Content-Type' => 'text/html']);
    }
}