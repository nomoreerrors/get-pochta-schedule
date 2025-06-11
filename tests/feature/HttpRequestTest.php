<?php
use GuzzleHttp\Client;
use Classes\PochtaParser;
use PHPUnit\Framework\TestCase;


class HttpRequestTest extends TestCase {
public function testWorkingStatusTagClassNameExists(): void {
    {
        $url = 'https://www.pochta.ru/offices/600901';
        $html = file_get_contents($url);
    
        $dom = new DOMDocument();
        $dom->loadHTML($html); // подавляем предупреждения
    
        $xpath = new DOMXPath($dom);
    
        $nodes = $xpath->query("//div[contains(@class, 'WorkingStatus__Status-sc-1omyyuw-0')]");
    
        // Проверка, что хотя бы один найден
        $this->assertGreaterThan(0, $nodes->length, 'Класс тега не обнаружен — возможно, изменилась структура сайта');
    }
    
}

public function testWorkingHoursTagClassNameExists(): void {
    {
        $url = 'https://www.pochta.ru/offices/600901';
        $html = file_get_contents($url);
    
        $dom = new DOMDocument();
        $dom->loadHTML($html); // подавляем предупреждения
    
        $xpath = new DOMXPath($dom);
    
        $nodes = $xpath->query("//table[contains(@class, 'WorkingHoursTable__Table-jcknjy-0')]//tr");
    
        // Проверка, что хотя бы один найден
        $this->assertGreaterThan(0, $nodes->length, 'Класс тега не обнаружен — возможно, изменилась структура сайта');
    }
    
}

}
