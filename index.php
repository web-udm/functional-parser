<?php

declare(strict_types=1);

use Symfony\Component\DomCrawler\Crawler;

require_once __DIR__ . '/vendor/autoload.php';

function getHtml(string $url): string {
    $filePath = __DIR__ . '/cache/' . md5($url);

    if (file_exists($filePath)) {
        return unserialize(file_get_contents($filePath));
    }

    $html = file_get_contents($url);
    file_put_contents($filePath, serialize($html));

    return $html;
}

function getCrawler(string $html): Crawler {
    return new Crawler($html);
}

function getMaxPage(Crawler $crawlerOfTopicsListPage): int {
    return (int) $crawlerOfTopicsListPage->filter('.PageNavNext + a')->text();
}

function getForumPagesUrls(string $topicsListUrl): array {
    return array_map(function ($pageNumber) use ($topicsListUrl) {
        return "$topicsListUrl/page-$pageNumber";
    }, range(1, getMaxPage(getCrawler($topicsListUrl))));
}

function getTopicsUrlsFromForumPage(string $topicsListUrl): array {
    return getCrawler($topicsListUrl)->filter('.PreviewTooltip')
        ->each(function (Crawler $topicCrawler) {
            return 'https://php.ru/forum/' . $topicCrawler->attr('href');
        });
}

//print_r(getTopicsUrlsFromForumPage('https://php.ru/forum/forums/php-dlja-novichkov.13/'));

$crawler = getCrawler(getHtml('https://php.ru/forum/forums/php-dlja-novichkov.13/'));
print_r($crawler->html());