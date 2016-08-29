<?php

namespace Biz\User\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface UserFamilyInfoDao extends GeneralDaoInterface
{
    public function deleteByUserId($userId);

    public function findFamilyMembers($userId);
 
    public function getTableFields();
}