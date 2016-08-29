<?php

namespace Biz\User\Dao\Impl;

use Biz\User\Dao\RosterDao;
use Codeages\Biz\Framework\Dao\GeneralDaoImpl;

class RosterDaoImpl extends GeneralDaoImpl implements RosterDao
{
    protected $table = 'roster';

    public function getByUserId($userId)
    {
        return $this->getByFields(array('userId'=>$userId));
    }

    public function getTableFields()
    {
        $sql = "SELECT * FROM {$this->table()}";
        $select = $this->db()->query($sql);
        $columnCount = $select->columnCount();
        for ($count=0;$count<$columnCount;$count++) {
            $meta = $select->getColumnMeta($count);
            $columns[] = $meta['name'];
        }
        
        return $columns ?: null;
    }
    
    public function declares()
    {
        return array(
            'conditions' => array(
                'id = :id',
                'name = :name',
                'department = :department'
            ),
        );
    }
}