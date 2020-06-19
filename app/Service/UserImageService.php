<?php

namespace App\Service;

use App\Repository\ImageRepository;
use Intervention\Image\Facades\Image as ImageOperetor;

/**
 * プロフィール編集時の画像に関するサービスクラス
 */
class UserImageService extends ImageService
{
    /**
     * 画像ファイルをストレージに保存し、ファイル情報をDBに保存します。
     *
     * @param \Illuminate\Http\UploadedFile $uploadFile
     * @param array $imageData
     * @return \App\Image
     */
    public function saveImage(\Illuminate\Http\UploadedFile $uploadFile, array $imageData)
    {
        $img = ImageOperetor::make($uploadFile);

        // リサイズ
        $img->resize(200, 200);

        // 画像をストレージに保存
        $fileName = uniqid() . '.' . $uploadFile->guessExtension();
        $this->saveStorage($img, $fileName);

        // 画像情報をDBに保存
        return $this->saveDb(array_merge($imageData, [
            'image_name' => $fileName,
        ]));
    }

    /**
     * imageIdに対応した画像と画像情報を削除します。
     *
     * @param array $imageIds
     * @return void
     */
    public function deleteImages(array $imageIds)
    {
        // imageIdから画像情報を取得
        $imageRepository = new ImageRepository();
        $imageArray = $imageRepository->getImages($imageIds)->get();

        // ストレージの画像を削除
        $this->deleteStorage($imageArray);

        // DBの画像パスを削除
        $this->deleteDb($imageIds);
    }
}
