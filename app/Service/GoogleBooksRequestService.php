<?php

namespace App\Service;

/**
 * GoogleBooksApiリクエストに関するサービスクラス
 */
class GoogleBooksRequestService
{
    /**
     * 検索クエリ
     *
     * @var string
     */
    private $query;

    /**
     * 検索する国
     *
     * @var string
     */
    private $country;

    /**
     * 検索最大数
     *
     * @var int
     */
    private $maxResults;

    /**
     * リクエストベースURL
     *
     * @var string
     */
    private $baseUrl;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->query = '';
        $this->countory = 'JP';
        $this->maxResults = 40; // max:40
        $this->baseUrl = 'https://www.googleapis.com/books/v1/volumes';
    }

    /**
     * allをセットします。
     *
     * @param string $all
     * @return void
     */
    public function setAll(string $all)
    {
        $this->query = $this->query . str_replace([" ", "　"], '+', $all) . '+';
    }

    /**
     * titleをセットします。
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title)
    {
        $this->query = $this->query . 'intitle:' . str_replace([" ", "　"], '+', $title) . '+';
    }

    /**
     * authorをセットします。
     *
     * @param string $author
     * @return void
     */
    public function setAuthor(string $author)
    {
        $this->query = $this->query . 'inauthor:' . str_replace([" ", "　"], '+', $author) . '+';
    }

    /**
     * isbnをセットします。
     *
     * @param string $isbn
     * @return void
     */
    public function setIsbn(string $isbn)
    {
        $this->query = $this->query . 'isbn:' . str_replace([" ", "　"], '+', $isbn) . '+';
    }

    /**
     * GoogleBooksにリクエストを送ります。
     *
     * @return mixed
     */
    public function fetchGoogleBooks()
    {
        $this->query = substr_replace($this->query, '', strlen($this->query) - 1);
        $param = '?q=' . $this->query . '&Country=' . $this->country . '&maxResults=' . $this->maxResults;
        $url = $this->baseUrl . $param;

        $option = [
            CURLOPT_RETURNTRANSFER => true, //文字列として返す
            CURLOPT_TIMEOUT => 5, // タイムアウト時間
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, $option);

        $json = curl_exec($ch);
        $info = curl_getinfo($ch);
        $errorNo = curl_errno($ch);

        // OK以外はエラーなので空白配列を返す
        if ($errorNo !== CURLE_OK) {
            // 詳しくエラーハンドリングしたい場合はerrorNoで確認
            // タイムアウトの場合はCURLE_OPERATION_TIMEDOUT
            return [];
        }

        // 200以外のステータスコードは失敗とみなし空配列を返す
        if ($info['http_code'] !== 200) {
            return [];
        }

        return json_decode($json);
    }
}
