<?php

namespace App\Service;

/**
 * 検索機能に関するサービスクラス
 */
class SearchService
{
    /**
     * 検索文字列を検索文字の配列に変換します。
     *
     * @param string $searchWord
     * @return array
     */
    public static function searchWordsOrganizer(string $searchWord)
    {
        $searchTemp = str_replace([' ', '　'], ' ', $searchWord);
        return explode(' ', $searchTemp);
    }
}
