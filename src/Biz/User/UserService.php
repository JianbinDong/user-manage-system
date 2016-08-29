<?php

namespace Biz\User;

interface UserService
{
    public function createUser($user);
    
    public function importUsers($users);
    
    public function register($user);

    public function updateAll($id, $fields);

    public function getCompleteInfo($id);

    public function searchAllUsers($conditions, $orderBy, $start, $limit);

    public function searchDepartmentUsers($conditions, $orderBy, $start, $limit);

    public function searchAllUserCounts($conditions);

    public function searchDepartmentUserCounts($conditions);
    /**
     * roster表
     */
    public function getRoster($userId);

    public function createRoster($fields);

    public function updateRoster($userId, $fields);
    
    /**
     * user表
     */
    public function updateUser($userId, $fields);

    public function getUser($userId);
    
    public function getUserByUsername($userName);

    public function getUserByNumber($number);
    
    public function uploadImage($userId, $image, $type);
    

    /**
     * user_basic_info表
     */
    public function getBasic($userId);

    /**
     * user_family_info表
     */
    public function findFamilyMembers($userId);

    /**
     * user_learn_info表
     */
    public function findEduExperiences($userId);

    /**
     * user_work_info表
     */
    public function findWorkExperiences($userId);

    /**
     * user_other_info表
     */
    public function getOtherInfoByUserId($userId);
}