<?php

namespace NeoP\Http\Message\Uploaded;

use Neop\Http\Message\Exception\UploadedFileException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFile extends \SplFileInfo implements UploadedFileInterface
{
    
    protected $size;
    protected $path;
    protected $tmpFile;
    protected $stream;
    protected $error;
    protected $clientFilename;
    protected $clientMediaType;
    protected $moved = false;

    public function __construct(string $tmpFile, int $size = 0, int $error = 0, string $clientFilename = "", string $clientMediaType = "")
    {
        $this->setError($error)
             ->setSize($size)
             ->setClientFilename($clientFilename)
             ->setClientMediaType($clientMediaType);
        $this->isOk() && $this->setFile($tmpFile);
        parent::__construct($tmpFile);
    }

    public static function parseFiles(array $files) :array
    {
        $datas = [];
        foreach ($files as $file) {
            $data = new static($file['tmp_name'], $file['size'], $file['error'], $file['name'], $file['type']);
            $datas[] = $data;
        }
        return $datas;
    }

    
    public function getStream()
    {
        return $this->stream;
    }
    
    private function setStream($stream)
    {
        $this->stream = $stream;
        return $this;
    }

    public function moveTo($targetPath)
    {
        if ($this->isMoved() || !$this->isOk()) {
            throw new UploadFileException('Error: Uploaded file is not ok or is already moved!');
        }

        if (empty($targetPath) || $targetPath == "") {
            throw new UploadFileException('Error: targe path is not Empty or null string');
        }

        if ($this->tmpFile) {
            $this->moved = php_sapi_name() == 'cli' ? rename($this->tmpFile, $targetPath) : move_uploaded_file($this->tmpFile, $targetPath);
        }

        if (! $this->moved) {
            throw new UploadFileException(sprintf('Uploaded file could not be move to %s', $targetPath));
        } else {
            $this->path = $targetPath;
        }
    }
    
    /**
     * Retrieve the file size.
     *
     * Implementations SHOULD return the value stored in the "size" key of
     * the file in the $_FILES array if available, as PHP calculates this based
     * on the actual size transmitted.
     *
     * @return int|null The file size in bytes or null if unknown.
     */
    public function getSize()
    {

    }

    private function setSize(int $size = 0)
    {
        $this->size = $size;
        return $this;
    }
    
    /**
     * Retrieve the error associated with the uploaded file.
     *
     * The return value MUST be one of PHP's UPLOAD_ERR_XXX constants.
     *
     * If the file was uploaded successfully, this method MUST return
     * UPLOAD_ERR_OK.
     *
     * Implementations SHOULD return the value stored in the "error" key of
     * the file in the $_FILES array.
     *
     * @see http://php.net/manual/en/features.file-upload.errors.php
     * @return int One of PHP's UPLOAD_ERR_XXX constants.
     */
    public function getError()
    {

    }

    private function setError(int $error = 0) 
    {
        $this->error = $error;
        return $this;
    }
    
    /**
     * Retrieve the filename sent by the client.
     *
     * Do not trust the value returned by this method. A client could send
     * a malicious filename with the intention to corrupt or hack your
     * application.
     *
     * Implementations SHOULD return the value stored in the "name" key of
     * the file in the $_FILES array.
     *
     * @return string|null The filename sent by the client or null if none
     *     was provided.
     */
    public function getClientFilename()
    {

    }

    private function setClientFilename(string $clientFilename)
    {
        $this->clientFilename = $clientFilename;
        return $this;
    }
    
    /**
     * Retrieve the media type sent by the client.
     *
     * Do not trust the value returned by this method. A client could send
     * a malicious media type with the intention to corrupt or hack your
     * application.
     *
     * Implementations SHOULD return the value stored in the "type" key of
     * the file in the $_FILES array.
     *
     * @return string|null The media type sent by the client or null if none
     *     was provided.
     */
    public function getClientMediaType()
    {

    }

    private function setClientMediaType(string $clientMediaType)
    {
        $this->clientMediaType = $clientMediaType;
        return $this;
    }
    
    private function setFile(string $file)
    {
        $this->tmpFile = $file;
        return $this;
    }
    
    private function isOk(): bool
    {
        return $this->error === UPLOAD_ERR_OK;
    }
    
    private function isMoved(): bool
    {
        return $this->moved;
    }
}