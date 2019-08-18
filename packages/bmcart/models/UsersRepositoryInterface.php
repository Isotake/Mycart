<?php

namespace Packages\Bmcart\Model;

interface UsersRepositoryInterface
{
    /**
     * Find a user by the given username
     *
     * @param $id
     * @return mixed
     */
    public function findByUsername($id);

    /**
     * Find a user by the given user_id
     *
     * @param $id
     * @return mixed
     */
    public function findById($id);

}