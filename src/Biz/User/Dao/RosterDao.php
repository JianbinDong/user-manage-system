<?php

namespace Biz\User\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface RosterDao extends GeneralDaoInterface
{
    public function getByUserId($userId);
}