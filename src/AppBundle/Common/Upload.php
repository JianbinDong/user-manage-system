<?php

namespace AppBundle\Common;

use Symfony\Component\Filesystem\Filesystem;

class Upload
{
    protected $file;

    public function __construct($file)
    {   
        $this->file = $file;
    }

    public function moveToDirectory($userId, $oldPath = null, $type)
    {   
        $fileSystem = new Filesystem();

        if (!$fileSystem->exists(__DIR__.'/../../../web/upload/')) {
            $fileSystem->mkdir(__DIR__.'/../../../web/upload/');
        }

        if (!empty($oldPath)) {
            $array = explode('/', $oldPath);
            $name = array_pop($array);
            $fileSystem->remove(__DIR__.'/../../../web/upload/'.$userId.'/'.$name);
        }
        
        $imgExtension = $this->file->getClientOriginalExtension();
        $imgName = $type.'.'.$imgExtension;
        $newDirectory = __DIR__.'/../../../web/upload/'.$userId;
        $this->file->move($newDirectory,$imgName);

        return 'upload/'.$userId.'/'.$imgName;
    }
}