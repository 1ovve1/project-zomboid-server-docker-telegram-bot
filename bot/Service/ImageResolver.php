<?php declare(strict_types=1);

namespace PZBot\Service;
use PZBot\Env;
use PZBot\Exceptions\Checked\PathWasNotFoundException;

class ImageResolver
{
  protected readonly string $uploadPath;
  protected readonly string $downloadPath;

  function __construct(string $uploadPath, string $downloadPath) {
    $this->uploadPath = $uploadPath;
    $this->downloadPath = $downloadPath;
  }

  static function fromEnv(Env $config): self
  {
    return new self(
      $config->get("BOT_UPLOAD_PATH"),
      $config->get("BOT_DOWNLOAD_PATH"),
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
    $path = $this->getUploadPath() . $dirPrefix;

    if (!is_dir($path)) {
        throw new PathWasNotFoundException($path);
    }

    // Filter the file list to only return images.
    $image_list = array_filter(scandir($path), function ($file) {
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        return in_array($extension, ['png', 'jpg', 'jpeg', 'gif']);
    });
    if (!empty($image_list)) {
        shuffle($image_list);
        return $path . '/' . $image_list[0];
    }

    throw new PathWasNotFoundException($path);
  }

  function getUploadPath(): string
  {
    return $this->uploadPath;
  }

  function getDownloadPath(): string
  {
    return $this->downloadPath;
  }
}