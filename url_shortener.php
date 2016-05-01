<?php

function shortenUrl($before_url){
    // APIキーの設定
    $api_key = 'AIzaSyAcV2owpZtm3o0C-zdtzc1X8T9Q9eWHec0' ;

    // GETメソッドで指定がある場合は上書き
    if( isset( $_GET['url'] ) && !empty( $_GET['url'] ) ) {
        $before_url = $_GET['url'] ;
    }

    // cURLを利用してリクエスト
    $curl = curl_init() ;
    curl_setopt( $curl, CURLOPT_URL , 'https://www.googleapis.com/urlshortener/v1/url?key=' . $api_key ) ;
    curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-type: application/json' ) ) ;   // JSONの送信
    curl_setopt( $curl, CURLOPT_CUSTOMREQUEST , 'POST' ) ;          // POSTメソッド
    curl_setopt( $curl, CURLOPT_POSTFIELDS, json_encode( array( 'longUrl' => $before_url ) ) ) ;        // 送信するJSONデータ
    curl_setopt( $curl, CURLOPT_HEADER, 1 ) ;                       // ヘッダーを取得する
    curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false ) ;           // 証明書の検証を行わない
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true ) ;            // curl_execの結果を文字列で返す
    curl_setopt( $curl, CURLOPT_TIMEOUT, 15 ) ;                     // タイムアウトの秒数
    curl_setopt( $curl, CURLOPT_FOLLOWLOCATION , true ) ;           // リダイレクト先を追跡するか？
    curl_setopt( $curl, CURLOPT_MAXREDIRS, 5 ) ;                    // 追跡する回数
    $res1 = curl_exec( $curl ) ;
    $res2 = curl_getinfo( $curl ) ;
    curl_close( $curl ) ;

    // 取得したデータ
    $json = substr( $res1, $res2['header_size'] ) ;     // 取得したデータ(JSONなど)
    $header = substr( $res1, 0, $res2['header_size'] ) ;    // レスポンスヘッダー (検証に利用したい場合にどうぞ)

    // 取得したJSONをオブジェクトに変換
    $obj = json_decode( $json ) ;

    // URLを表示用に整形 (検証用)
    foreach( array( 'before_url', ) as $variable_name ) {
        ${ $variable_name } = htmlspecialchars( ${ $variable_name } , ENT_QUOTES , 'UTF-8' ) ;
    }

    // HTML用
    $html = '' ;

    // 出力
    $html .= '<h2>実行結果</h2>' ;

    // 成功時
    if( isset( $obj->id ) && !empty( $obj->id ) ) {
        // 取得した短縮URL
        $shorten_url = $obj->id ;

        // 出力
        $html .= '<dl>' ;
        $html .=    '<dt>オリジナルURL</dt>' ;
        $html .=        '<dd><a href="' . $before_url . '" target="_blank">' . $before_url . '</a></dd>' ;
        $html .=    '<dt>短縮したURL</dt>' ;
        $html .=        '<dd><a href="' . $shorten_url . '" target="_blank">' . $shorten_url . '</a></dd>' ;
        $html .= '</dl>' ;

    // 失敗時
    } else {
        $html .= '<p><mark>短縮URLを作成できませんでした…。</mark></p>';

    }

    // 取得したデータ
    $html .= '<h2>取得したデータ</h2>' ;
    $html .= '<h3>JSONの内容</h3>' ;
    $html .= '<textarea>' ;
    $html .=    ( ($json!='') ? $json : '取得できませんでした…。' ) ;
    $html .= '</textarea>' ;
    $html .= '<h3>レスポンスヘッダーの内容</h3>' ;
    $html .= '<textarea>' ;
    $html .=    ( $header ? $header : '取得できませんでした…。' ) ;
    $html .= '</textarea>' ;

    // URLの入力
    $html .= '<h2>URLの指定</h2>' ;
    $html .= '<form>' ;
    $html .=    '<p><input name="url" placeholder="https://syncer.jp" value="' . $before_url . '"></p>' ;
    $html .=    '<p><button>SHORTEN !!</button></p>' ;
    $html .= '</form>' ;

    // ブラウザに[$html]の内容を出力
    // 運用時はHTMLのヘッダーとフッターを付けましょう。
    // echo $html ;
    return $shorten_url;
}

?>