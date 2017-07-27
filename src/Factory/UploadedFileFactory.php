<?php
/**
 * @link http://www.atomframework.net/
 * @copyright Copyright (c) 2017 Safarov Alisher
 * @license https://github.com/atomwares/atom-http/blob/master/LICENSE (MIT License)
 */

namespace Atom\Http\Factory;

use Atom\Http\Message\UploadedFile;
use Interop\Http\Factory\UploadedFileFactoryInterface;

/**
 * Class UploadedFileFactory
 *
 * @package Atom\Http\Factory
 */
class UploadedFileFactory implements UploadedFileFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createUploadedFile(
        $file,
        $size = null,
        $error = UPLOAD_ERR_OK,
        $clientFilename = null,
        $clientMediaType = null
    ) {
        return new UploadedFile($file, $size, $error, $clientFilename, $clientMediaType);
    }

    /**
     * @param array $files
     *
     * @return UploadedFile[]
     */
    public function createUploadedFilesFromArray(array $files)
    {
        $uploadedFiles = [];

        if (isset($files['tmp_name']) && ! is_array($files['tmp_name'])) {
            $uploadedFiles[] = new UploadedFile(
                $files['tmp_name'],
                $files['size'],
                $files['error'],
                $files['name'],
                $files['type']
            );
        } elseif (isset($files['tmp_name']) && is_array($files['tmp_name'])) {
            foreach ($files['tmp_name'] as $key => $file) {
                $uploadedFiles[] = new UploadedFile(
                    $file,
                    $files['size'][$key],
                    $files['error'][$key],
                    $files['name'][$key],
                    $files['type'][$key]
                );
            }
        } else {
            foreach ($files as $key => $value) {
                $uploadedFiles[$key] = $this->createUploadedFilesFromArray($value);
            }
        }

        return $uploadedFiles;
    }
}
