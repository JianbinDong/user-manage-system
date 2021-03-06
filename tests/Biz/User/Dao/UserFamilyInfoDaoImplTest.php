<?php

use Codeages\Biz\Framework\UnitTests\BaseTestCase;

class UserFamilyInfoDaoImplTest extends BaseTestCase
{   
    public function testAddUser()
    {
    }
    
    public function testGetTableFields()
    {
        $familyMemberInfo = array(
            'userId' => 1,
            'member' => '爸爸',
            'trueName' => '董剑斌',
            'age' => 17,
            'job' => '浙江台州程序员',
            'phone' => '15757125389'
        );

        $familyMember = $this->getFamilyMemberDao()->create($familyMemberInfo);

        $fields = $this->getFamilyMemberDao()->getTableFields();

        $this->assertEquals($fields, array_keys($familyMember));
    }

    /**
     * @dataProvider additionProvider
     */
    public function testFindFamilyMembers($data)
    {   
        $this->getUserServiece()->createUser($data);
        $familyMembers = $this->getFamilyMemberDao()->findFamilyMembers(1);
        $this->assertEquals($data['family'],$familyMembers);
    }

    protected function getUserServiece()
    {
        return self::$kernel['user_service'];
    }

    protected function getFamilyMemberDao()
    {
        return self::$kernel['family_member_dao'];
    }

    public function additionProvider()
    {
        return [
            [array(
                "basic" => array(
                    "id" => 1,
                    "userId" => 1,
                    "departmentId" => 1,
                    "rank" => "p20",
                    "number" => "0010",
                    "trueName" => "陆昉宇",
                    "phone" => 13411231234,
                    "email" => "594@qq.com",
                    "gender" => "male",
                    "bornTime" => 1994,
                    "native" => "中国海宁",
                    "nation" => "汉族",
                    "height" => "177cm",
                    "weight" => "55kg",
                    "blood" => "AB",
                    "education" => "博士",
                    "prefession" => "计科",
                    "joinTime" => 1470823339,
                    "marriage" => 0,
                    "residence" => "海宁",
                    "address" => "没考虑",
                    "postcode" => 310000,
                    "Idcard" => 330481199412170055,
                    "professionTitle" => "PHP程序员",
                    "householdType" => "城市",
                    "recordPlace" => "杭州",
                    "formerLaborShip" => "已解除",
                    "politics" => "群众",
                ),
                "family" => array(
                    array(
                        "id" => 1,
                        "userId" => 1,
                        "member" => "爸爸",
                        "trueName" => "陆昉宇",
                        "age" => 30,
                        "job" => "那几款",
                        "phone" => 13511292312,
                    ),
                    array(
                        "id" => 2,
                        "userId" => 1,
                        "member" => "爸爸",
                        "trueName" => "陆昉宇",
                        "age" => 30,
                        "job" => "那几款",
                        "phone" => 13511292312,
                    )
                ),
                "work" => array(
                    array(
                        "id" => 1,
                        "userId" => 1,
                        "startTime" => 1470823339,
                        "endTime" => 1470823339,
                        "company" => "方法",
                        "position" => "发的",
                        "leaveReason" => "等等",
                    )
                ),
                "education" => array(
                    array(
                        "id" => 1,
                        "userId" => 1,
                        "startTime" => 1470823339,
                        "endTime" => 1470823339,
                        "schoolName" => "你",
                        "profession" => "方法",
                        "position" => "方法",
                    ),
                    array(
                        "id" => 2,
                        "userId" => 1,
                        "startTime" => 1470823339,
                        "endTime" => 1470823339,
                        "schoolName" => "你",
                        "profession" => "方法",
                        "position" => "方法",
                    )
                ),
                "other" => array(
                    "id" => 1,
                    "userId" => 1,
                    "reward" => "覅UN会不会就不喝酒",
                    "selfAssessment" => "和基本和金额为备份和文件",
                )
            )]
        ];
    }
}
