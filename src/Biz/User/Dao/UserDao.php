<?php

namespace Biz\User\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface UserDao extends GeneralDaoInterface
{
    public function getByNumber($number);

    public function getByUsername($username);

    public function findByStatus($status);

    public function getTableFields();
}