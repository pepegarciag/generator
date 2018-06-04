<?php

namespace Kodeloper\Generator\Utils;

class FileSystemUtil
{
    public static function createFile($path, $file, $contents)
    {
        self::createDirectoryIfNotExist($path);
        $path = $path.$file;
        file_put_contents($path, $contents);
    }

    public static function createDirectoryIfNotExist($path, $replace = false)
    {
        if (file_exists($path) && $replace) {
            rmdir($path);
        }
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
    }

    public static function deleteFile($path, $file)
    {
        if (file_exists($path.$file)) {
            return unlink($path.$file);
        }

        return false;
    }
}
