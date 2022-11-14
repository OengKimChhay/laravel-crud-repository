<?php

namespace App\Helper\FileUpload;

use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FileUploadService
{
    const ALLOWED_FILE_EXTENSION = ['pdf', 'jpg', 'png', 'docx', 'doc', 'xls', 'pptx', 'ppt', 'txt'];
    const DIR = 'image';
    const SIZE = [1000, 600];
    const SHAPE = ['resize','fit'];

    /**
     * Uploader Disk
     * @return Filesystem|Storage
     */
    public function storage(?string $disk = 'public'): FileSystem|Storage
    {
        return Storage::disk($disk);
    }


    /**
     * Upload single file to folder
     * @param Illuminate\Http\UploadedFile|Illuminate\Http\File $file
     * @param array $allowFileType
     * @param string|null $directory
     * @return string Uploaded path
     */
    public function uploadFile(File|UploadedFile $file, array $allowFileType, string $directory = self::DIR): array
    {
        $this->checkFileType($file, $allowFileType);

        $path = $this->storage()->put($directory, $file);
        return [
            'path' => $path, // store path to database
            'url' => $this->url($path), // for display image
            'name' => $file->getClientOriginalName(),
            'fileExtension' => $this->getFileType($file)
        ];
    }


    /**
     * Upload multi file to folder
     * @param array $file
     * @param string|null $directory
     * @return string Uploaded path
     */
    public function uploadMultiFile(array $files, array $allowFileType, string $directory = self::DIR): array
    {
        // check file each file if has an extension not allowed
        foreach ($files as $file) {
            $this->checkFileType($file, $allowFileType);
        }

        $paths = [];
        foreach ($files as $file) {
            $paths[] = $this->uploadFile($file, $allowFileType, $directory);
        }
        return $paths;
    }


    /**
     * @param File|UploadedFile $image
     * @param string $shape 
     * @param array $allowFileType
     * @param array $size
     * @param string $directory
     * @return array
     */
    public function createAndUploadImage(File|UploadedFile $image, string $shape, array $allowFileType, array $size = self::SIZE, string $directory = self::DIR): array
    {
        /**
         * base on https://image.intervention.io
         * we set $shape as var to define API function, so below is available function
         * $shape = 'fit','resize'
         */
        // check file type
        $this->checkFileType($image, $allowFileType);

        // check image shape
        $checkShape = in_array($shape, self::SHAPE);
        !$checkShape && throw new Exception('Shape not allowed to upload, shape allowed '. join(',',self::SHAPE));

        [$width, $height] = $size;

        $createImage = Image::make($image->path());
        $createImage = match($shape){
            'resize' => $createImage->resize($width, $height),
            'fit' => $createImage->fit($width, $height),
            default => $createImage->fit($width, $height)
        };

        $name = $image->hashName();
        $path = $directory . '/' . $width . 'x' . $height . '_' . $name;
        $this->storage()->put($path, $createImage->encode('png', 100));

        return [
            'path' => $path, // store path to database
            'url' => $this->url($path), // for display image
            'name' => $image->getClientOriginalName(),
            'fileExtension' => $this->getFileType($image)
        ];
    }

    /**
     * @param string|array $path
     */
    public function delete(string|array $path)
    {
        return $this->storage()->delete($path);
    }

    /**
     * string $path
     */
    public function deleteMultiFile(array $paths)
    {
        return $this->delete($paths);
    }


    /**
     * Response file url from path
     * @param string|null $path
     * @param string|null $disk
     * @return string full url of the file
     */
    public function url(?string $path, ?string $disk = null): ?string
    {
        return $this->storage($disk)->url($path);
    }


    /**
     * return file extension.
     */
    function getFileType($file)
    {
        $fileName = $file->getClientOriginalName();
        $mimeType = pathinfo($fileName, PATHINFO_EXTENSION);
        return $mimeType;
    }


    /**
     * Throw an HttpException when file type not allowed.
     */
    function checkFileType($file, $allowFileType)
    {
        $allowed = $allowFileType ?? self::ALLOWED_FILE_EXTENSION;
        $extension = $file->getClientOriginalExtension();
        $checkFileExtension = in_array($extension, $allowed);
        abort_if(!$checkFileExtension, 400, '.' . $extension . ' file type not allowed to upload, allowed files '.join(',',$allowed));
    }
}
