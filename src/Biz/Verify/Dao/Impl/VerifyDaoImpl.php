<?php

namespace Biz\Verify\Dao\Impl;

use Biz\Verify\Dao\VerifyDao;
use Codeages\Biz\Framework\Dao\GeneralDaoImpl;

class VerifyDaoImpl extends GeneralDaoImpl implements VerifyDao
{
    protected $table = 'number_verify';

    public function getByUser($userId)
    {
        return $this->getByFields(array('userId'=>$userId));
    }

    public function declares()
    {
        return array(
            'serializes' => array(),
            'conditions' => array(
                'userId = :userId',
            ),
        );
    }
}