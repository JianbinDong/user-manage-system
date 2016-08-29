<?php 

namespace Biz\Verify;

interface VerifyService
{
    public function deleteVerify($id);
    
    public function getVerifyByUserId($userId);

    public function searchVerifies($conditions, $order, $start, $limit);   
}