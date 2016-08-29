<?php

namespace Biz\Verify\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface VerifyDao extends GeneralDaoInterface
{
    public function getByUser($userId);

    public function search($conditions, $order, $start, $limit);
}