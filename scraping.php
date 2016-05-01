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
        $rankingUrl = 'https://www.amazon.co.jp/gp/bestsellers/books/2278488051/';
        $rankingResultHtml = getHtmlData($rankingUrl);
        $rankRowArray = array();
        $n = 0;
        foreach ($rankingResultHtml[".zg_itemRow"] as $rankRow) {
            $rankTitle = pq($rankRow)->find('.zg_title')->find('a')->text();
            $rankStar = pq($rankRow)->find('.a-icon-star')->find('span')->text();
            $rankLink = pq($rankRow)->find('a')->attr('href');
            $rankImg = pq($rankRow)->find('img')->attr('src');

            $responseHeaderText = "「".$original_title."」だと、ちょとわからない...代わりにこれでも嫁。最近人気。";

            $rankSet = array(
                $n => array(
                    "respose_header" => $responseHeaderText,
                    "comic_title"    => $rankTitle,
                    "comic_star"     => $rankStar,
                    "comic_link"     => shortenUrl($rankLink),
                    "comic_img"      => $rankImg
                )
            );
            array_push($rankRowArray, $rankSet);
            $n++;
        }
        $randKey = rand(0,19);
        $resultSet = array_values($rankRowArray[$randKey]);
        var_dump($resultSet);
        return $resultSet[0];
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
        return $resultSet;
    }
}

function getHtmlData($url){
    $opts = array(
            'http'=>array(
                    'method'=>"GET",
                    'header'=>"User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.57 Safari/537.17"
                    )
            );
    $context = stream_context_create($opts);
    // mb_language('Japanese');
    $htmlData = file_get_contents($url, false, $context);
    //$htmlData = mb_convert_encoding($htmlData, 'utf8', 'auto');
    //var_dump($htmlData);
    $htmlData = phpQuery::newDocument($htmlData);

    return $htmlData;
}

?>
