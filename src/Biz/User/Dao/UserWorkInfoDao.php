<?php

namespace Biz\User\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface UserWorkInfoDao extends GeneralDaoInterface
{
    public function deleteByUserId($userId);
    
    public function findWorkExperiences($userId);

    public function getTableFields();
}