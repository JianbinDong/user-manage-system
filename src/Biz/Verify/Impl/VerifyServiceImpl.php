<?php 

namespace Biz\Verify\Impl;

use Biz\Verify\VerifyService;
use Codeages\Biz\Framework\Service\BaseService;

class VerifyServiceImpl extends BaseService implements VerifyService
{
    public function deleteVerify($id)
    {
        return $this->getVerifyDao()->delete($id);
    }

    public function getVerifyByUserId($userId)
    {
        return $this->getVerifyDao()->getByUser($userId);
    }

    public function searchVerifies($conditions, $order, $start, $limit)
    {
        return $this->getVerifyDao()->search($conditions, $order, $start, $limit);
    }

    protected function getVerifyDao()
    {
        return $this->biz['verify_dao'];
    }
}