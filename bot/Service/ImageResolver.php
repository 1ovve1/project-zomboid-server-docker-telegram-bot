<?php declare(strict_types=1);

namespace PZBot\Service;
use PZBot\Database\PicturesHistory;
use PZBot\Exceptions\Checked\PathWasNotFoundException;

class ImageResolver
{
  protected readonly string $uploadPath;
  protected readonly string $downloadPath;

  function __construct(string $uploadPath, string $downloadPath) {
    $this->uploadPath = $uploadPath;
    $this->downloadPath = $downloadPath;
  }

  static function fromEnv(): self
  {
    return new self(
      env("BOT_UPLOAD_PATH"),
      env("BOT_DOWNLOAD_PATH"),
    );
  }

  /**
   * Return random pic from upload path
   *
   * @param string $dirPrefix
   * @return string
   * @throws PathWasNotFoundException
   */
  function getRandomPicturePath(string $dirPrefix = ''): string
  {
    $path = $this->getUploadPath($dirPrefix);
    $imageList = $this->getAllPictures($dirPrefix);

    if (!empty($imageList)) {
        shuffle($imageList);

        return $path . '/' . $imageList[0];
    }

    throw new PathWasNotFoundException($path);
  }

 /**
   * Return random pic from upload path
   *
   * @param string $dirPrefix
   * @return string
   * @throws PathWasNotFoundException
   */
  function getRandomPicturePathUnique(string $dirPrefix = ''): string
  {
    $path = $this->getUploadPath($dirPrefix);
    $imageList = $this->getAllPictures($dirPrefix);

    if (!empty($imageList)) {
        $picturesHistory = PicturesHistory::getAllPicturesHistoryByKey($dirPrefix);

        $imageListDiff = array_diff($imageList, $picturesHistory);

        if (empty($imageListDiff)) {
            PicturesHistory::deletePicturesHistoryByKey($dirPrefix);
            $imageListDiff = $imageList;
        }

        shuffle($imageListDiff);

        $image = $imageListDiff[0];

        PicturesHistory::insertPictureByKey($dirPrefix, $image);

        return $path . '/' . $image;
    }

    throw new PathWasNotFoundException($path);
  }

  /**
   * @param string $dirPrefix
   * @return array
   */
  protected function getAllPictures(string $dirPrefix = ''): array
  {
    $path = $this->getUploadPath($dirPrefix);

    if (!is_dir($path)) {
        throw new PathWasNotFoundException($path);
    }

    // Filter the file list to only return images.
    return array_filter(scandir($path), function ($file) {
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        return in_array($extension, ['png', 'jpg', 'jpeg', 'gif']);
    });
  }

  function getUploadPath(string $dirPrefix = ''): string
  {
    return $this->uploadPath . $dirPrefix;
  }

  function getDownloadPath(string $dirPrefix = ''): string
  {
    return $this->downloadPath . $dirPrefix;
  }
}