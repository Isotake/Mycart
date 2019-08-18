<?php

namespace Packages\Bmcart\Model;

use Packages\Bmcart\Model\PrePointDetailsEloquent;
use Packages\Bmcart\Model\PrePointDetailsRepositoryInterface;

class PrePointDetailsRepository implements PrePointDetailsRepositoryInterface
{

    protected $model = null;

    public function __construct()
    {
        $this->model = new PrePointDetailsEloquent();
    }

    public function getPrePointDetails($pre_point_id)
    {
        return $this->model
            ->where('pre_point_id', $pre_point_id)
            ->select(
                'pre_point_details.id as pre_point_detail_id',
                'pre_point_details.order_id',
                'pre_point_details.order_details_id',
                'pre_point_details.user_id',
                'pre_point_details.pre_point_id',
                'pre_point_details.point',
                'pre_point_details.created_at',
                'pre_point_details.modified_at'
            )
            ->get();
    }

    /**
     * @param $order_new_id
     * @param $bm_user_id
     * @param $legacy_user_id
     * @param $pre_point_id
     * @param $order_cards_new_ids
     * @return \Packages\Bmcart\Model\PrePointDetailsEloquent|null
     */
    public function insertPrePointDetails ($order_id, $user_id, $pre_point_id, $order_details_ids)
    {
        $entity = $this->model;
        $timestamp = date("Y-m-d H:i:s", time());

        $insert_data = [];
        foreach ($order_details_ids as $key => $order_detail) {
            $order_details_id = $order_detail['id'];
            $card_point = $order_detail['card_point'];
            $insert_data[] = [
                'order_id' => (int)$order_id,
                'order_details_id' => (int)$order_details_id,
                'user_id' => (int)$user_id,
                'pre_point_id' => (int)$pre_point_id,
                'point' => (int)$card_point,
                'created_at' => $timestamp,
                'modified_at' => $timestamp,
            ];
        }

        $entity->insert($insert_data);

        return $entity;

    }
}