<?php
// phpQueryの読み込み
require_once("phpQuery-onefile.php");
require_once("url_shortener.php");

function scrape($original_title){
    $title = $original_title.' 1巻 マンガ';
    $titleForUrl = urlencode($title);
    $url = 'https://www.amazon.co.jp/s/&field-keywords='.$titleForUrl;

    $topResultHtml = getHtmlData($url);
    $resultChkFlg = pq($topResultHtml['#noResultsTitle'])->text();

    if (strlen($resultChkFlg) > 0){
                $resultSet = array(
            "respose_header" => '「'.$original_title.'」だと、ちょとわからない...'
        );
    } else {
        $topResult = $topResultHtml["#result_0"];
        $topResultComicDetailLink = pq($topResult)->find('a')->attr('href');
        $topResultComicImg = pq($topResult)->find('img')->attr('src');

        $topResultComicTitleHtmlEntities = pq($topResult)->find('h2')->attr('data-attribute');
        $topResultComicTitle =  html_entity_decode($topResultComicTitleHtmlEntities);

        $topResultComicStar = pq($topResult['.a-icon-star'])->find('span')->text();

        $responseHeaderText = "えーっと、それはもしかして、こちらですか？";

        $resultSet = array(
            "respose_header" => $responseHeaderText,
            "comic_title"    => $topResultComicTitle,
            "comic_star"     => $topResultComicStar,
            "comic_link"     => shortenUrl($topResultComicDetailLink),
            "comic_img"      => $topResultComicImg
        );
        var_dump($resultSet);    
    }

    return $resultSet;
}

function getHtmlData($url){
    $opts = array(
            'http'=>array(
                    'method'=>"GET",
                    'header'=>"User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.57 Safari/537.17"
                    )
            );
    $context = stream_context_create($opts);
    $htmlData = file_get_contents($url, false, $context);
    $htmlData = phpQuery::newDocument($htmlData);

    return $htmlData;
}

?>
