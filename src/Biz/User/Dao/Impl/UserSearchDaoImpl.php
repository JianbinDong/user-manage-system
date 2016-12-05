<?php

namespace Biz\User\Dao\Impl;

use Biz\User\Dao\UserSearchDao;
use Biz\User\Dao\Impl\UserSearchDaoImpl;
use Codeages\Biz\Framework\Dao\GeneralDaoImpl;

class UserSearchDaoImpl extends GeneralDaoImpl implements UserSearchDao
{
    public function searchAll($conditions, $orderBy, $start, $limit)
    {
        $mysql = '';
        $params = array();
        if (isset($conditions['searchTime'])) {
            $timeType = $conditions['searchTime'];
            unset($conditions['searchTime']);
        }

        foreach ($conditions as $key => $value) {
            if ($key == 'startTime') {
                $mysql .= " ? <= {$timeType} AND ";
                $params[] = $value;
            } elseif ($key == 'endTime') {
                $mysql .= " ? >= {$timeType} AND ";
                $params[] = $value;
            } elseif ($key == 'trueName') {
                $mysql .= $key .' LIKE ' .'?' .' AND ';
                $params[] = '%'.$value.'%';
            } else {
                $mysql .= $key .'=?' .' AND ';
                $params[] = $value;
            }
        }
        $mysql = rtrim($mysql,' AND ');
        
        $sql = "SELECT * FROM user_basic LEFT JOIN department ON user_basic.departmentId = department.id LEFT JOIN user ON user.id = user_basic.userId WHERE {$mysql} AND number > 0 ORDER BY {$orderBy[0]} {$orderBy[1]} LIMIT {$start},{$limit}";
        return $this->db()->fetchAll($sql, $params) ?: array();
    }

    public function searchDepartmentUsers($conditions, $orderBy, $start, $limit)
    {   
        $mysql = '';
        $params = array();
        foreach ($conditions as $key => $value) {
            if ($key == 'trueName') {
                $mysql .= $key .' LIKE ' .'\'' .'?' .'\'' .' AND ';
                $params[] = '%'.$value.'%';
            } else {
                $mysql .= $key .'=? AND ';
                $params[] = $value;
            }
        }
        $mysql = rtrim($mysql,' AND ');

        $sql = "SELECT * FROM user_basic LEFT JOIN department ON user_basic.departmentId = department.id LEFT JOIN user ON user.id = user_basic.userId WHERE {$mysql} AND number > 0 ORDER BY {$orderBy[0]} {$orderBy[1]} LIMIT {$start},{$limit}";
        
        return $this->db()->fetchAll($sql, $params) ?: array();
    }

    public function searchAllCounts($conditions)
    {
        $mysql = '';
        $params = array();
        if (isset($conditions['searchTime'])) {
            $timeType = $conditions['searchTime'];
            unset($conditions['searchTime']);
        }
        foreach ($conditions as $key => $value) {
            if ($key == 'startTime') {
                $mysql .= " ? <= {$timeType} AND ";
                $params[] = $value;
            } elseif ($key == 'endTime') {
                $mysql .= " ? >= {$timeType} AND ";
                $params[] = $value;
            } elseif ($key == 'trueName') {
                $mysql .= $key .' LIKE ? AND ';
                $params[] = '%'.$value.'%';
            } else {
                $mysql .= $key .'=? AND ';
                $params[] = $value;
            }
        }
        $mysql = rtrim($mysql,' AND ');
        $sql = "SELECT COUNT(*) FROM user_basic LEFT JOIN department ON user_basic.departmentId = department.id LEFT JOIN user ON user.id = user_basic.userId WHERE {$mysql} AND number > 0";
            $userCount = $this->db()->fetchAll($sql, $params) ?: array();
            return $userCount[0]['COUNT(*)'];
    }

    public function searchDepartmentUserCounts($conditions)
    {
        $mysql = '';
        $params = array();
        foreach ($conditions as $key => $value) {
            if ($key == 'trueName') {
                $mysql .= $key .' LIKE ? AND ';
                $params[] = '%'.$value.'%';
            } else {
                $mysql .= $key .'=? AND ';
                $params[] = $value;
            }
        }
        $mysql = rtrim($mysql,' AND ');

        $sql = "SELECT COUNT(*) FROM user_basic LEFT JOIN department ON user_basic.departmentId = department.id LEFT JOIN user ON user.id = user_basic.userId WHERE {$mysql} AND number > 0";
            $userCount = $this->db()->fetchAll($sql, $params) ?: array();
            return $userCount[0]['COUNT(*)'];
    }

    public function declares()
    {
        return array(
            'timestamps' => array('createdTime', 'updatedTime'),
            'conditions' => array(
                'id = :id',
                'status = :status',
                'number = :number',
            ),
        );
    }
}