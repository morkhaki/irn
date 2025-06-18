<?php

$rss_urls = [
    "https://ir.voanews.com/api/zkup_l-vomx-tpejiyy",
    "https://www.radiofarda.com/api/zpoqil-vomx-tpe_kip",
    "https://www.bbc.com/persian/index.xml"
];

$all_news = [];

foreach ($rss_urls as $url) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; GitHub Actions)',
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $rss_data = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if (!$rss_data || $err) {
        continue; // اگر خطا دارد، برو سراغ فید بعدی
    }

    $xml = @simplexml_load_string($rss_data, 'SimpleXMLElement', LIBXML_NOCDATA);
    if (!$xml || !isset($xml->channel->item)) {
        continue;
    }

    foreach ($xml->channel->item as $item) {
        $all_news[] = [
            "title" => (string)$item->title,
            "link" => (string)$item->link,
            "pubDate" => (string)$item->pubDate,
            "description" => (string)$item->description,
            "source" => parse_url($url, PHP_URL_HOST)
        ];
    }
}

// مرتب‌سازی بر اساس زمان
usort($all_news, function ($a, $b) {
    return strtotime($b['pubDate']) <=> strtotime($a['pubDate']);
});

// ذخیره در فایل JSON
file_put_contents("news.json", json_encode($all_news, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));


?>
