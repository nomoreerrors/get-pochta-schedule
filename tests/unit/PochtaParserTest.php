<?php
use PHPUnit\Framework\TestCase;
use Classes\PochtaParser;

class PochtaParserTest extends TestCase
{
    public function testFetchInitializesDomDocumentAndXpath()
    {
        $url = 'https://www.pochta.ru/offices/600901';
        $parser = new PochtaParser($url);
        
        $parser->fetch();
        
        // Проверяем, что $doc - это объект DOMDocument
        $docProperty = (new ReflectionClass($parser))->getProperty('doc');
        $docProperty->setAccessible(true);
        $doc = $docProperty->getValue($parser);
        $this->assertInstanceOf(DOMDocument::class, $doc);
        
        // Проверяем, что $xpath - это объект DOMXPath
        $xpathProperty = (new ReflectionClass($parser))->getProperty('xpath');
        $xpathProperty->setAccessible(true);
        $xpath = $xpathProperty->getValue($parser);
        $this->assertInstanceOf(DOMXPath::class, $xpath);
    }


    public function testGetWorkingStatusReturnsParsedData()
    {
        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="ru">
        <head><meta charset="UTF-8"></head>
        <body>
          <div class="WorkingStatus__Status-sc-1omyyuw-0">Открыто</div>
        </body>
        </html>
        HTML;


        $parser = $this->getMockBuilder(PochtaParser::class)
            ->setConstructorArgs(['http://fake-url.test'])
            ->onlyMethods(['fetchHtml'])
            ->getMock();

        $parser->method('fetchHtml')->willReturn($html);

        $parser->fetch();
        $status = $parser->getWorkingStatus();

        $this->assertEquals('Открыто', $status);
    } 

    public function testGetWorkingHoursReturnsCorrectData() 
    {
        $path = __DIR__ . '/../fakes/fakeWorkingHours.html';
        $this->assertFileExists($path);
        $html = file_get_contents($path);     

        $parser = $this->getMockBuilder(PochtaParser::class)
            ->setConstructorArgs(['http://fake-url.test'])
            ->onlyMethods(['fetchHtml'])
            ->getMock();

        $parser->method('fetchHtml')->willReturn($html);
        $parser->fetch();
        $hours = $parser->getWorkingHours();
        $this->assertEquals([
            "Понедельник — 09:00–18:00.\n Перерыв: 13:00–14:00 ",
            "Вторник — 09:00–18:00 "
        ], $hours);
    }


}
