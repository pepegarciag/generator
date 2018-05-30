<?php

namespace Kodeloper\Generator\Generators;

use Kodeloper\Generator\Utils\FileSystemUtil;

class BaseGenerator
{   
    public function generateFile ($path, $file, $content) {
        FileSystemUtil::createDirectoryIfNotExist($path);
        $this->rollbackOldFile($path, $file);
        // TODO GENERATE FILE WITH CONTENT AND STUBS
    }

    public function rollbackOldFile($path, $file)
    {
        if (file_exists($path.$file)) {
            if (!copy($path.$file, now()->format('d_m_Y_h_i_s') . '_' .$path.$file . '._bkp')) {
                return false;
            } else {
                return FileSystemUtil::deleteFile($path, $file);
            }
        }
        return false;
    }


}
