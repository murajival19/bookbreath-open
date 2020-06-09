<?php

namespace App\Service;

use App\Repository\ImageRepository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * 画像に関する抽象クラス
 */
abstract class ImageService
{
    /**
     * ファイルディレクトリパス
     *
     * @var string
     */
    private $fileDirectoryPath;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->fileDirectoryPath = storage_path('app/public/image/');
    }

    /**
     * 画像をストレージに保存します。
     *
     * @param \Intervention\Image\Image $img
     * @param string $fileName
     * @return void
     */
    public function saveStorage(\Intervention\Image\Image $img, string $fileName)
    {
        $img->save($this->fileDirectoryPath . $fileName);
    }

    /**
     * 画像情報をDBに保存します。
     *
     * @param array $imageData
     * @return \App\Image
     */
    public function saveDb(array $imageData)
    {
        $imageRepository = new ImageRepository();
        return $imageRepository->setImage($imageData);
    }

    /**
     * ストレージにある画像を削除します。
     *
     * @param array|\Illuminate\Support\Collection $imageArray
     * @return void
     */
    public function deleteStorage($imageArray)
    {
        // ストレージの画像を削除
        foreach ($imageArray as $image) {
            $filePath = $this->fileDirectoryPath . $image->image_name;
            if (File::exists($filePath)) {
                unlink($filePath);
                Storage::delete($image);
            }
        }
    }

    /**
     * 画像情報をDBから削除します。
     *
     * @param array $deleteImageIds
     * @return void
     */
    public function deleteDb(array $deleteImageIds)
    {
        $imageRepository = new ImageRepository();
        $imageRepository->deleteImages($deleteImageIds);
    }
}
