<?php

namespace Biz\Common;

use Codeages\Biz\Framework\Context\Kernel;

class BaseService
{
    protected $biz;

    public function __construct(Kernel $biz)
    {
        $this->biz = $biz;
    }

    public function getCurrentUser()
    {
        return $this->biz->user();
    }
}
