<?php

declare(strict_types=1);

use Symfony\Component\DomCrawler\Crawler;

require_once __DIR__ . '/vendor/autoload.php';

$url = 'https://php.ru/forum/forums/ide.46';

function getHtml(string $url): string {
    $filePath = __DIR__ . '/cache/' . md5($url);

    if (file_exists($filePath)) {
        return unserialize(file_get_contents($filePath));
    }

    $html = file_get_contents($url);
    file_put_contents($filePath, serialize($html));

    return $html;
}

function getCrawler(string $url): Crawler {
    return new Crawler(getHtml($url));
}

function getMaxPage(string $url): int {
    return (int) getCrawler($url)->filter('.PageNav nav > a:nth-last-child(2)')->text();
}

function getForumPages(string $topicsListUrl): array {
    return array_map(function ($pageNumber) use ($topicsListUrl) {
        return "$topicsListUrl/page-$pageNumber";
    }, range(1, getMaxPage(($topicsListUrl))));
}

function getThreadsFromOnePage(string $topicsListUrl): array {
    return getCrawler($topicsListUrl)->filter('.PreviewTooltip')
        ->each(function (Crawler $topicCrawler) {
            return 'https://php.ru/forum/' . $topicCrawler->attr('href');
        });
}

function getAllThreads(string $forumUrl): array {
    return array_reduce(getForumPages($forumUrl), function($resArray, $item) {
        return array_merge($resArray, getThreadsFromOnePage($item));
    }, []);
}

print_r(getAllThreads($url));