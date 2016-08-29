<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Common\ArrayToolkit;
use AppBundle\Common\PHPExcelToolkit;
use AppBundle\Common\Paginator;
use AppBundle\Controller\UserBaseController;

class ExportController extends UserBaseController
{
    public function exportUserListAction(Request $request)
    {
        $conditions = $request->query->all();
        
        $datas = $this->getdatas($conditions);

        $filename   = '员工信息.xls';
        $execelInfo = $this->_makeInfo();

        $objWriter  = PHPExcelToolkit::export($datas, $execelInfo);
        $this->_setHeader($filename);
        $objWriter->save('php://output');
    }

    protected function getdatas($conditions)
    {
        $list = $this->listUsers($conditions);
        $users = $list['users'];

        $title = array('员工工号','姓名','部门','职级','手机号','邮箱','性别','出生日期','民族','籍贯','身高','体重','血型','文化程度','专业','婚姻','户口所在地','邮编','身份证','职称','户口性质','户口所在地','档案存放地','与原工作的劳动关系','报道日期','政治面貌','家庭成员称呼','姓名','年龄','工作单位及岗位职务','联系电话','开始时间','结束时间','院校名称','所学专业','担任职务','开始时间','结束时间','工作单位','岗位职务','离职原因','所受培训及所具有证书','自我评价'
        );

        $keys = array('number','trueName','departmentId','rank','phone','email','gender','bornTime','nation','native','height','weight','blood','education','prefession','marriage','address','postcode','Idcard','professionTitle','householdType','residence','recordPlace','formerLaborShip','joinTime','politics','member','trueName','age','job','phone','startTime','endTime','schoolName','profession','position','startTime','endTime','company','position','leaveReason','reward','selfAssessment'
        );

        foreach ($users as $key => $user) {
            $users[$key] = $this->parts($user, $keys);
            $users[$key] = $this->translateData($users[$key]);

        }
        $keys[2] = 'department';

        $title = array($title, $keys);
        $datas = array_merge($title, $users);

        return $datas;
    }

    protected function _makeInfo()
    {
        $info              = array();
        $info['creator']   = '阔知';
        $info['sheetName'] = '员工信息';
        return $info;
    
    }

    protected function _setHeader($filename)
    {
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename={$filename}");
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
    }

    protected function translateData($data)
    {
        $data['departmentId'] = $this->getDepartmentService()->getDepartment($data['departmentId'])['name'];
        if ($data['marriage']) {
            $data['marriage'] = '已婚';
        } else {
            $data['marriage'] = '未婚';
        }

        if ($data['gender'] == 'male'){
            $data['gender'] = '男';
        } else {
            $data['gender'] = '女';
        }

        $data['joinTime'] = date("Y-m-d", $data['joinTime']);
        $data['bornTime'] = date("Y-m-d", $data['bornTime']);

        switch ($data['education']) {
            case 'doctor':
                $data['education'] = '博士';
                break;

            case 'master':
                $data['education'] = '硕士';
                break;
                
            case 'regularCollege':
                $data['education'] = '本科';
                break;
                
            case 'juniorCollege':
                $data['education'] = '大专';
                break;
                
            case 'seniorMiddle':
                $data['education'] = '高中';
                break;
            
            default:
                $data['education'] = '初中';
                break;
        }

        switch ($data['politics']) {
            case 'other':
                $data['politics'] = '其他';
                break;
            
            case 'partyMember':
                $data['politics'] = '共产党员';
                break;
            
            case 'reservePartyMember':
                $data['politics'] = '预备党员';
                break;
            
            case 'leagueMember':
                $data['politics'] = '团员';
                break;
            
            default:
                $data['politics'] = '群众';
                break;
        }

        return $data;
    }

    protected static function parts(array $array, array $keys)
    {
        $new = array();
        foreach ($keys as $key) {
            if (in_array($key, array_keys($array))) {
                $new[$key] = $array[$key];
            }
        }
        return $new;
    }
}