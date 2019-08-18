<?php

namespace Packages\Bmcart\Model;

use Packages\Bmcart\Model\UserPointLogsEloquent;
use Packages\Bmcart\Model\UserPointLogsRepositoryInterface;

class UserPointLogsRepository implements UserPointLogsRepositoryInterface
{

    protected $model = null;

    public function __construct()
    {
        $this->model = new UserPointLogsEloquent();
    }

    public function getUserPointLogs($user_id)
    {
        return $this->model
            ->join('user_point_reasons', 'user_point_logs.user_point_reason_id', '=', 'user_point_reasons.id')
            ->where('user_id', $user_id)
            ->select([
                'user_id',
                'operator_id',
                'point',
                'user_point_reasons.name as user_point_reason',
                'comment',
                'user_point_logs.created_at',
                'user_point_logs.modified_at',
            ])
            ->get();
    }

    public function insertUserPointLogs($user_id, $operator_id, $user_point, $user_point_reason_id, $comment)
    {
        $entity = $this->model;
        $timestamp = date("Y-m-d H:i:s", time());

        $entity->user_id = (int)$user_id;
        $entity->operator_id = (int)$operator_id;
        $entity->point = (int)$user_point;
        $entity->user_point_reason_id = (int)$user_point_reason_id;
        $entity->comment = $comment;
        $entity->created_at = $timestamp;
        $entity->modified_at = $timestamp;
        $entity->save();
        return $entity;
    }

}