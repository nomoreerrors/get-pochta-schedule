<?php
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    public function testCleanText():void
    {
        $input = "  Тест\u{00A0}с  неразрывным\u{2009}и\u{202F}тонким\u{FEFF}пробелами  \n\nНовая строка \t и   лишние   пробелы ";
        $expected = "Тест с неразрывным и тонким пробелами Новая строка и лишние пробелы";

        $this->assertEquals($expected, cleanText($input));
    }
}