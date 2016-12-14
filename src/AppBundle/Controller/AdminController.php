<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Common\ArrayToolkit;
use AppBundle\Common\Import;
use AppBundle\Common\Paginator;
use AppBundle\Controller\UserBaseController;
use AppBundle\Common\LdapProcesser;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AdminController extends UserBaseController
{
    public function createUserAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $fields = $request->request->all();

            $user = $this->getUserService()->createUser($fields);

            $this->sendPasswordEmail($user['email']);
            
            return $this->redirect($this->generateUrl('admin_user_present_list'));
        }

        $departmentsChoices = $this->getDepartmentChoices();

        return $this->render('AppBundle:User:add/add-user.html.twig', array(
            'departmentsChoices' => $departmentsChoices
        ));        
    }

    public function editUserAction(Request $request, $id)
    {
        $currentUser = $this->getUser();
        if ($request->getMethod() == 'POST') {
            $fields = $request->request->all();

            $this->getUserService()->updateAll($id, $fields);
            if ($currentUser['id'] == $id) {
                return $this->redirect($this->generateUrl('user_roster',array('userId'=>$id)));
            } else {
                return $this->redirect($this->generateUrl('admin_user_present_list'));
            }
        }

        $user = $this->getUserService()->getCompleteinfo($id);

        $departmentsChoices = $this->getDepartmentChoices();

        return $this->render('AppBundle:User:edit/edit-user.html.twig', array(
            'user' => $user['user'],
            'basic' => $user['basic'],
            'familyMembers' => $user['familyMembers'],
            'eduExperiences' => $user['eduExperiences'],
            'workInfos' => $user['workInfos'],
            'otherInfo' => $user['otherInfo'],
            'extraInfo' => array(
                'nav' => 'edit_user',
            ),
            'departmentsChoices' => $departmentsChoices
        ));

    }

    public function listPresentAction(Request $request)
    {
        $conditions = $request->query->all();
        $conditions['status'] = 'on';
        
        $list = $this->listUsers($conditions);

        $departmentsChoices = $this->getDepartmentChoices();

        return $this->render('AppBundle:User:list/list-user.html.twig',array(
            'status' => 'on',
            'users' => $list['users'],
            'paginator' => $list['paginator'],
            'userCount' => $list['userCount'],
            'departmentsChoices' => $departmentsChoices
        ));
    }

    public function listDemissionAction(Request $request)
    {
        $conditions = $request->query->all();
        $conditions['status'] = 'off';
        
        $list = $this->listUsers($conditions);

        $departmentsChoices = $this->getDepartmentChoices();
        
        return $this->render('AppBundle:User:list/list-user.html.twig',array(
            'status' => 'off',
            'users' => $list['users'],
            'paginator' => $list['paginator'],
            'userCount' => $list['userCount'],
            'departmentsChoices' => $departmentsChoices
        ));
    }

    public function downloadAction(Request $request, $id, $fileName)
    {
        $user = $this->getUserService()->getUser($id);
        $basic = $this->getUserService()->getBasic($id);
        $path = $user[$fileName];
        preg_match('/\.\w+$/', $path, $exten);
        if ($fileName == 'imgEducation') {
            $fileName = '学历证书';
        } elseif($fileName == 'imgRank') {
            $fileName = '职称证书';
        } else {
            $fileName = '身份证';
        }
        if (!file_exists($path)) {
            throw new NotFoundHttpException('文件找不到');
        } else {
            $file = fopen($path, "r");       
            Header("Content-type: application/octet-stream");
            Header("Accept-Ranges: bytes");
            Header("Accept-Length: ".filesize($path));
            Header("Content-Disposition: attachment; filename=" . $basic['trueName'].'-'.$fileName.$exten[0]);
            echo fread($file,filesize($path));
            fclose($file);
            return new JsonResponse(true);
        }
    }

    public function certificateAction(Request $request, $id, $type)
    {
        $user = $this->getUserService()->getUser($id);
        $basic = $this->getUserService()->getBasic($user['id']);
        $user['trueName'] = $basic['trueName'];

        return $this->render('AppBundle:User:show/certificate.html.twig', array(
            'user' => $user,
            'type' => $type
        ));
    }

    public function exitJobAction(Request $request, $id)
    {
        if ($request->getMethod() == 'POST') {
            $fields = $request->request->all();

            $this->getUserService()->updateUser($id, array(
                'status' => 'off',
                'quitTime' => strtotime($fields['quitTime'])
            ));

            return new JsonResponse(array('userId'=>$id));
        }

        return $this->render('AppBundle:User:change-jobstatus-modal.html.twig', array(
            'id' => $id,
            'status' => 'on'
        ));
    }

    public function entryJobAction(Request $request, $id)
    {
        if ($request->getMethod() == 'POST') {
            $fields = $request->request->all();

            $this->getUserService()->updateUser($id, array(
                'status' => 'on',
                'joinTime' => strtotime($fields['joinTime'])
            ));

            return new JsonResponse(array('userId'=>$id));
        }

        return $this->render('AppBundle:User:change-jobstatus-modal.html.twig', array(
            'id' => $id,
            'status' => 'off'
        ));
    }
    
    public function changeUserRoleAction(Request $request, $id)
    {
        if ($request->getMethod() =='POST') {
            $fields = $request->request->all();

            if (empty($fields['roles'])) {
                $fields['roles'] = array('ROLE_USER');
            }

            $roles = $this->getUserService()->updateUser($id, array('roles' => $fields['roles']));
            
            return new JsonResponse(true);
        }
        $user = $this->getUserService()->getUser($id);
        return $this->render('AppBundle:User:change-role-modal.html.twig', array(
            'user'=> $user,
        ));
    }

    public function checkNumberAction(Request $request)
    {
        $number = $request->query->get('number');
        $user = $this->getUserService()->getUserByNumber($number);
        if (empty($user)) {
            return new JsonResponse(true);
        } else {
            return new JsonResponse(false);
        }
    }

    public function importAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $tmpFile = $_FILES['file_stu']['tmp_name'];
            $import = new Import();
            $users = $import->import($tmpFile);

            foreach ($users as $user) {
                $affected = $this->getUserService()->importUser($user);
                if (!empty($affected)) {
                    $this->sendPasswordEmail($affected['email']);
                }
            }

            return $this->redirect($this->generateUrl('admin_user_present_list'));
        }

        return $this->render('AppBundle:User:import.html.twig');
    }

    public function passNumberAction(Request $request, $userId)
    {
        $verify = $this->getVerifyService()->getVerifyByUserId($userId);

        $this->getUserService()->updateUser($userId, array('number'=>$verify['number']));

        $this->getVerifyService()->deleteVerify($verify['id']);

        return new JsonResponse(true);
    }

    public function verifyNumberAction(Request $request)
    {
        $conditions = array();
        $verifies = $this->getVerifyService()->searchVerifies($conditions, array('id', 'ASC'),0,999);

        return $this->render('AppBundle:User:verify-number.html.twig', array(
            'verifies' => $verifies
        ));
    }

    public function updateLdapAction(Request $request)
    {
        $user = $this->getUser();
        if ($user['username'] == 'admin' && $user['number'] == '0000') {
            $process = new LdapProcesser($this->biz);
            $process->updateAllUserLdapInfo($user['id']);
            return new JsonResponse('update ldap success');
        }

        throw AccessDeniedHttpException('!!!');
    }

    protected function sendPasswordEmail($to)
    {
        $message = \Swift_Message::newInstance()
        ->setSubject('员工密码通知')
        ->setFrom('dongjianbin@howzhi.com')
        ->setTo($to)
        ->setBody(
            $this->renderView(
                'AppBundle:User:test.txt.twig', array(
                'password' => 'kaifazhe'
            )),
            'text/html'
        );

        $this->get('mailer')->send($message);
    }

    protected function getVerifyService()
    {
        return $this->biz['verify_service'];
    }
}
