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
        $nodes = $this->xpath->query("//div[contains(@class, 'WorkingStatus__Status-r1ynz2-0')]");
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
    //    var_dump($array);
    //    exit;
        
        $data = [];
        $rows = $this->xpath->query("//table[contains(@class, 'WorkingHoursTable__Table-a0obnx-0')]//tr");
        foreach ($rows as $tr) {
            if ($tr instanceof \DOMElement) {
                $cols = $tr->getElementsByTagName('td');
                if ($cols->length >= 2) {
                    $day = cleanText($cols[0]->textContent);
                    $hours = cleanText($cols[1]->textContent);
                    $data[] = $this->parseLine("$day — $hours");
                }
            }
        }
        return $data;
    }

    /**
     * Разбить полученную строку на массив ключ-значение
     * @param string $line
     * @return array{break: null, date: null, hours: null|array{break: string|null, date: string|null, hours: string|null}}
     */
    function parseLine(string $line): array {
        // Шаг 1: находим первую позицию дефиса
        $firstDashPos = mb_strpos($line, '—');
    
        // Обрезаем дату
        $date = trim(mb_substr($line, 0, $firstDashPos));
    
        // Модифицируем строку: убираем всё до и включая первый дефис, а также пробелы с боков
        $line = trim(mb_substr($line, $firstDashPos + 1));
    
        // Шаг 2: ищем "перерыв"
        $breakPos = mb_stripos($line, 'перерыв');
    
        if ($breakPos !== false) {
            $hours = trim(mb_substr($line, 0, $breakPos));
    
            // Снова модифицируем строку, убирая "перерыв" и всё до него
            $line = trim(mb_substr($line, $breakPos + mb_strlen('перерыв')));
            $break = $line;
        } else {
            $hours = trim($line);
            $break = null;
        }
    
        return [
            'date' => $date,
            'hours' => $hours,
            'break' => $break,
        ];
    }
    
    

    /**
     * Вынесен для удобной работы с mock 
     * @return bool|string
     */
    protected function fetchHtml(): string {
        return file_get_contents($this->url);
    }
}
