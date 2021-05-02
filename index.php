<?php

declare(strict_types=1);

use Symfony\Component\DomCrawler\Crawler;

require_once __DIR__ . '/vendor/autoload.php';

function getCrawlerByUrl(string $url): Crawler {
    return new Crawler(file_get_contents($url));
}

function getMaxPage(Crawler $crawlerOfTopicsListPage): int {
    return (int) $crawlerOfTopicsListPage->filter('.PageNavNext + a')->text();
}

print_r(
    getMaxPage(
        getCrawlerByUrl('https://php.ru/forum/forums/php-dlja-novichkov.13/')));