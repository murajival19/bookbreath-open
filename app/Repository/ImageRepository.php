<?php

namespace App\Repository;

use App\Image;

/**
 * 画像に関するリポジトリクラス
 */
class ImageRepository
{
    /**
     * 画像情報をDBに保存します。
     *
     * @param array $imageData
     * @return \App\Image
     */
    public function setImage(array $imageData)
    {
        $image = new Image();
        $image->fill($imageData)->save();
        return $image;
    }

    /**
     * 指定IDの画像情報を取得します。
     *
     * @param array $imageIds
     * @return \App\Image
     */
    public function getImages(array $imageIds)
    {
        return Image::whereIn('id', $imageIds);
    }

    /**
     * 指定投稿IDの画像情報を取得します。
     *
     * @param int $postId
     * @return \App\Image
     */
    public function getPostImages(int $postId)
    {
        return Image::where('post_id', $postId);
    }

    /**
     * 指定IDの画像情報を削除します。
     *
     * @param array $imageIds
     * @return void
     */
    public function deleteImages(array $imageIds)
    {
        Image::whereIn('id', $imageIds)->delete();
    }

    /**
     * 指定投稿IDの画像情報を削除します。
     *
     * @param int $postId
     * @return void
     */
    public function deletePostImages(int $postId)
    {
        Image::where('post_id', $postId)->delete();
    }
}
