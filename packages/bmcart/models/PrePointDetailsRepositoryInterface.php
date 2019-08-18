<?php

namespace Packages\Bmcart\Model;

interface PrePointDetailsRepositoryInterface
{
    /**
     * @param $usename
     * @return mixed
     */
    public function getPrePointDetails($user_id);

    public function insertPrePointDetails ($order_id, $user_id, $pre_point_id, $order_details_ids);
}