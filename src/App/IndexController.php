<?php

namespace App;

use Framework\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IndexController
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $quaryParams = $request->getQueryParams();
        $naam = $quaryParams['naam'] ?? 'Niet een bekende bezoeker.'; // soort base case

        $html = '<html><body>';
        $html .= '<h1>Kieswijzer</h1>';
        $html .= '<p>Hallo ' . htmlspecialchars($naam) . '!</p>';
        $html .= '<p>Voorbeeld stelling: Nederland moet meer investeren in duurzame energie.</p>';
        $html .= '<form method="POST" action="/results">';
        $html .= '<select name="answers[1]">';
        $html .= '<option value="-2">Helemaal oneens</option>';
        $html .= '<option value="-1">Oneens</option>';
        $html .= '<option value="0" selected>Neutraal</option>';
        $html .= '<option value="1">Eens</option>';
        $html .= '<option value="2">Helemaal eens</option>';
        $html .= '</select><br>';
        $html .= '<button type="submit">Resultaten bekijken</button>';
        $html .= '</form>';
        $html .= '</body></html>';

        return new Response($html, 200, "1.1", ['Content-Type' => 'text/html']);
    }
}