<?php 

namespace Biz\Department;

interface DepartmentService
{
    public function createDepartment($department);

    public function deleteDepartment($id);

    public function updateDepartment($id, $fields);

    public function getDepartment($id);

    public function getDepartmentByName($departmentName);

    public function findDepartments();
}