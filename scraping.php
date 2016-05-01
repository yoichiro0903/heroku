<?php
// phpQueryの読み込み
require_once("phpQuery-onefile.php");

function scrape($title){
    $title = $title.' 1巻 マンガ';
    $titleForUrl = urlencode($title);
    $url = 'https://www.amazon.co.jp/s/&field-keywords='.$titleForUrl;

    $topResultHtml = getHtmlData($url);
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
        "comic_link"     => $topResultComicDetailLink,
        "comic_img"      => $topResultComicImg
    );
    var_dump($resultSet);
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

    //html取得
    // $ch = curl_init();
    // curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_HEADER, FALSE);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    // curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.57 Safari/537.17');
    // $htmlData = curl_exec($ch);
    // curl_close($ch);

    // //文字化け対策
    // mb_language('Japanese');
    // $htmlData = mb_convert_encoding($htmlData,'utf8', 'auto');
    // var_dump($htmlData);
    //sleep(10);
}

?>
