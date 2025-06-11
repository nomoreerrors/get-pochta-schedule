<?php
namespace Classes;

class PochtaParser {
    private $url;
    private $doc;
    private $xpath;

    public function __construct(string $url) {
        $this->url = $url;
    }

    /**
     * @function libxml_use_internal_errors работает наподобие try/catch для предупреждений об ошибках xml-документов
     * Это позволяет избавиться от предупреждений в браузере и их сохранения в коде html
     * @return void
     */
    
    public function fetch() {
        $html = $this->fetchHtml(); // ← используем вместо прямого file_get_contents
        $this->doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $this->doc->loadHTML($html);
        libxml_clear_errors();
        $this->xpath = new \DOMXPath($this->doc);
    }

    /**
     * Get today working status by tag class name
     * @return string|null
     */
    public function getWorkingStatus(): ?string {
        $nodes = $this->xpath->query("//div[contains(@class, 'WorkingStatus__Status-sc-1omyyuw-0')]");
        if ($nodes->length > 0) {
            return cleanText($nodes[0]->textContent);
        }
        return null;
    }

    /**
     * Get working hours from <table> by class name
     * @return string[]
     */
    public function getWorkingHours(): array {
        $data = [];
        $rows = $this->xpath->query("//table[contains(@class, 'WorkingHoursTable__Table-jcknjy-0')]//tr");
        foreach ($rows as $tr) {
            if ($tr instanceof \DOMElement) {
                $cols = $tr->getElementsByTagName('td');
                if ($cols->length >= 2) {
                    $day = cleanText($cols[0]->textContent);
                    $hours = cleanText($cols[1]->textContent) . ' ';
                    $hours = preg_replace('/(перерыв)/iu', ".\n Перерыв:", $hours);

                    $data[] = "$day — $hours";
                }
            }
        }
        return $data;
    }


    /**
     * Вынесен для удобной работы с mock 
     * @return bool|string
     */
    protected function fetchHtml(): string {
        return file_get_contents($this->url);
    }
}
