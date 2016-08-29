<?php

namespace Biz\User\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface UserOtherInfoDao extends GeneralDaoInterface
{
    public function getByUserId($userId);

    public function getTableFields();
}