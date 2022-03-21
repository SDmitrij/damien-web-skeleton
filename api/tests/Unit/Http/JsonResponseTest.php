<?php

namespace Test\Unit\Http;

use App\Http\JsonResponse;
use PHPUnit\Framework\TestCase;
use stdClass;

class JsonResponseTest extends TestCase
{
    /**
     * @dataProvider getCases
     * @param mixed $source
     * @param mixed $expected
     */
    public function test($source, $expected): void
    {
        $response = new JsonResponse($source);

        self::assertEquals($expected, $response->getBody()->getContents());
        self::assertEquals(200, $response->getStatusCode());
    }

    public function getCases(): array
    {
        $class = new stdClass();
        $class->name = 'Damien';
        $class->born = 1997;

        return [
            'null' => [null, 'null'],
            'empty' => ['', '""'],
            'number' => [12, '12'],
            'string' => ['hello', '"hello"'],
            'array' => [['name' => 'Damien', 'born' => 1997], '{"name":"Damien","born":1997}'],
            'object' => [$class, '{"name":"Damien","born":1997}']
        ];
    }
}