<?php

declare(strict_types=1);

use Symfony\Component\DomCrawler\Crawler;

require_once __DIR__ . '/vendor/autoload.php';

function getCrawlerByUrl(string $topicsListUrl): Crawler {
    return new Crawler(file_get_contents($topicsListUrl));
}

function getMaxPage(Crawler $crawlerOfTopicsListPage): int {
    return (int) $crawlerOfTopicsListPage->filter('.PageNavNext + a')->text();
}

function getForumPages(string $topicsListUrl): array {
    $maxPageNumber = getMaxPage(getCrawlerByUrl($topicsListUrl));
    $pagesArr = [];

    foreach (range(1, $maxPageNumber) as $pageNumber) {
        $pagesArr[] = "$topicsListUrl/page-$maxPageNumber";
    }

    return $pagesArr;
}

print_r(getForumPages('https://php.ru/forum/forums/php-dlja-novichkov.13'));