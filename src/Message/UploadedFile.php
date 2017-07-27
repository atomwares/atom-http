<?php
/**
 * @link http://www.atomframework.net/
 * @copyright Copyright (c) 2017 Safarov Alisher
 * @license https://github.com/atomwares/atom-http/blob/master/LICENSE (MIT License)
 */

namespace Atom\Http\Message;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class UploadedFile
 *
 * @package Atom\Http\Message
 */
class UploadedFile implements UploadedFileInterface
{
    /**
     * @var StreamInterface
     */
    protected $stream;
    /**
     * @var resource|string
     */
    protected $file;
    /**
     * @var int|null
     */
    protected $size;
    /**
     * @var int
     */
    protected $error;
    /**
     * @var string
     */
    protected $clientFilename;
    /**
     * @var string
     */
    protected $clientMediaType;
    /**
     * @var bool
     */
    protected $moved;

    /**
     * @var array
     */
    private static $errors = [
        UPLOAD_ERR_OK,
        UPLOAD_ERR_INI_SIZE,
        UPLOAD_ERR_FORM_SIZE,
        UPLOAD_ERR_PARTIAL,
        UPLOAD_ERR_NO_FILE,
        UPLOAD_ERR_NO_TMP_DIR,
        UPLOAD_ERR_CANT_WRITE,
        UPLOAD_ERR_EXTENSION,
    ];

    /**
     * UploadedFile constructor.
     *
     * @param string|resource $file
     * @param int|null $size
     * @param int $error
     * @param string|null $clientFilename
     * @param string|null $clientMediaType
     */
    public function __construct(
        $file,
        $size = null,
        $error = UPLOAD_ERR_OK,
        $clientFilename = null,
        $clientMediaType = null
    ) {
        if (! is_string($file) && ! is_resource($file)) {
            throw new InvalidArgumentException(
                'Invalid file provided for UploadedFile; must be a string or resource'
            );
        }
        $this->file = $file;

        if ($size !== null && ! is_int($size)) {
            throw new InvalidArgumentException(
                'Invalid size provided for UploadedFile; must be null or an int'
            );
        }
        $this->size = $size;

        if (! is_int($error) || ! in_array($error, static::$errors)) {
            throw new InvalidArgumentException(
                'Invalid error status for UploadedFile; must be an UPLOAD_ERR_* constant'
            );
        }
        $this->error = $error;

        if ($clientFilename !== null && ! is_string($clientFilename)) {
            throw new InvalidArgumentException(
                'Invalid client filename provided for UploadedFile; must be null or a string'
            );
        }
        $this->clientFilename = $clientFilename;

        if ($clientMediaType !== null && ! is_string($clientMediaType)) {
            throw new InvalidArgumentException(
                'Invalid client media type provided for UploadedFile; must be null or a string'
            );
        }
        $this->clientMediaType  = $clientMediaType ;
    }

    /**
     * @inheritdoc
     */
    public function getStream()
    {
        if ($this->error !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Cannot retrieve stream due to upload error');
        }

        if ($this->moved) {
            throw new RuntimeException('Cannot retrieve stream after it has already been moved');
        }

        if ($this->stream === null) {
            $this->stream =  new Stream(is_string($this->file) ? fopen($this->file, 'r') : $this->file);
        }

        return $this->stream;
    }

    /**
     * @inheritdoc
     */
    public function moveTo($targetPath)
    {
        if ($this->moved) {
            throw new RuntimeException('Cannot move file; already moved!');
        }

        if ($this->error !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Cannot retrieve stream due to upload error');
        }

        if (! is_string($targetPath) || $targetPath === '') {
            throw new InvalidArgumentException(
                'Invalid path provided for move operation; must be a non-empty string'
            );
        }

        if (! is_dir($targetPath) || ! is_writable($targetPath)) {
            throw new RuntimeException(sprintf(
                'The target directory `%s` does not exists or is not writable',
                $targetPath
            ));
        }

        if (is_string($this->file)) {
            if (strpos(PHP_SAPI, 'cli') !== false) {
                $this->moved = rename($this->file, $targetPath);
            } else {
                if (! is_uploaded_file($this->file)) {
                    throw new RuntimeException(sprintf('`%s` is not a valid uploaded file', $this->file));
                }

                $this->moved = move_uploaded_file($this->file, $targetPath);
            }
        } else {
            $this->moved = (bool)stream_copy_to_stream($this->file, fopen($targetPath, 'w'));
        }

        if ($this->moved === false) {
            throw new RuntimeException(
                sprintf('Uploaded file could not be moved to `%s`', $targetPath)
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @inheritdoc
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @inheritdoc
     */
    public function getClientFilename()
    {
        return $this->clientFilename;
    }

    /**
     * @inheritdoc
     */
    public function getClientMediaType()
    {
        return $this->clientMediaType;
    }
}
