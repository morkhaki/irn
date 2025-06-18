<?php
$rss_url = "https://ir.voanews.com/api/zkup_l-vomx-tpejiyy";
$rss_data = @file_get_contents($rss_url);
if (!$rss_data) {
    file_put_contents("news.json", json_encode([
        "error" => "خطا در دریافت فید",
        "timestamp" => date("c")
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    exit(1);
}

$xml = simplexml_load_string($rss_data, 'SimpleXMLElement', LIBXML_NOCDATA);
$items = $xml->channel->item;

$news = [];
foreach ($items as $item) {
    $news[] = [
        "title" => (string)$item->title,
        "link" => (string)$item->link,
        "pubDate" => (string)$item->pubDate,
        "description" => (string)$item->description,
    ];
}

file_put_contents("news.json", json_encode($news, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
?>
