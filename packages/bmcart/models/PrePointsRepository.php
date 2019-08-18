<?php

namespace Packages\Bmcart\Model;

use Packages\Bmcart\Model\PrePointsEloquent;
use Packages\Bmcart\Model\PrePointsRepositoryInterface;

class PrePointsRepository implements PrePointsRepositoryInterface
{

    protected $model = null;

    public function __construct()
    {
        $this->model = new PrePointsEloquent();
    }

    public function getPrePoints($user_id)
    {
        return $this->model
//            ->leftJoin('pre_point_details', 'pre_points.id', '=', 'pre_point_details.pre_point_id')
            ->leftJoin('pre_point_consumes', 'pre_points.pre_point_consume_id', '=', 'pre_point_consumes.id')
            ->where('pre_points.user_id', $user_id)
            ->select(
                'pre_points.id as pre_point_id',
                'pre_points.order_id',
                'pre_points.user_id',
                'pre_points.operator_id',
                'pre_points.point',
                'pre_points.is_valid',
                'pre_points.comment',
                'pre_points.created_at',
                'pre_points.modified_at',
//                'pre_point_details.order_cards_new_id',
//                'pre_point_details.point as point_details',
                'pre_point_consumes.name as name_consumes'
            )
            ->get();
    }

    public function getUserGetPointLastInsertId($order_id)
    {
        return $this->model
            ->where('order_id', $order_id)
            ->first();
    }

    /**
     * @param $order_id
     * @param $user_id
     * @param $operator_id
     * @param $point_get
     * @param $use_point
     * @return \Packages\Bmcart\Model\PrePointsEloquent|null
     */
    public function insertPrePoints ($order_id, $user_id, $operator_id, $point_get, $use_point)
    {
        $entity = $this->model;
        $timestamp = date("Y-m-d H:i:s", time());
        $entity->insert([
            [
                "order_id" => (int)$order_id,
                "user_id" => (int)$user_id,
                "operator_id" => (int)$operator_id,
                "point" => (int)$point_get,
                "is_valid" => 1,
                "pre_point_consume_id" => 1,
                "comment" => '',
                "created_at" => $timestamp,
                "modified_at" => $timestamp
            ],
            [
                "order_id" => (int)$order_id,
                "user_id" => (int)$user_id,
                "operator_id" => (int)$operator_id,
                "point" => (int)$use_point,
                "is_valid" => 1,
                "pre_point_consume_id" => 4,
                "comment" => '',
                "created_at" => $timestamp,
                "modified_at" => $timestamp
            ],
        ]);

        return $entity;

    }
}