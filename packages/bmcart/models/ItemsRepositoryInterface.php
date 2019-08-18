<?php

namespace Packages\Bmcart\Model;

interface ItemsRepositoryInterface
{
    /**
     * Find a user by the given legacy_user_id
     *
     * @param $id
     * @return mixed
     */
    public function findById($item_id);

    public function findByIdForSharedLock($item_id);

    public function findByIdForExclusiveLock($item_id);

}