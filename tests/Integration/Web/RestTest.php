<?php
namespace Alura\Leilao\Tests\Integration\Web;

use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertStringContainsString;

class RestTest extends TestCase
{
    public function testApiRestDeveRetornar()
    {
        $resposta = file_get_contents('http://localhost:8080/rest.php');

        self:assertStringContainsString('200 OK', $http_response_header[0]);
        self::assertIsArray(json_decode($resposta));
    }
}
