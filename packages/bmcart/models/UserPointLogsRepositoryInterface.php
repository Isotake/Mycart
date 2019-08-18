<?php

namespace Packages\Bmcart\Model;

interface UserPointLogsRepositoryInterface
{
    /**
     * @param $usename
     * @return mixed
     */
    public function getUserPointLogs($user_id);

    public function insertUserPointLogs($user_id, $operator_id, $user_point, $user_point_reason_id, $comment);
}