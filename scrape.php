<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Panther\Client;

date_default_timezone_set('Asia/Jakarta');

$today = new DateTime();
$pageUrl = $today->format('d') % 2 === 0
    ? 'https://quotes.toscrape.com/page/2/'
    : 'https://quotes.toscrape.com/';

echo "📅 " . $today->format('l, d M Y') . " | 📄 $pageUrl\n";

$client = Client::createChromeClient(null, ['--headless', '--no-sandbox', '--disable-dev-shm-usage']);

$crawler = $client->request('GET', $pageUrl);

$data = [];
$crawler->filter('.quote')->each(function ($node) use (&$data) {
    if (count($data) >= 10) return;
    $data[] = [
        'text' => $node->filter('.text')->text(),
        'author' => $node->filter('.author')->text(),
    ];
});

$client->quit();

$outputDir = __DIR__ . '/public';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

$outputPath = $outputDir . '/data.json';
file_put_contents($outputPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "✅ Data scraped and saved to $outputPath\n";