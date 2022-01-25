<?php


namespace Stru\StruHyperfAuth;


interface UserProvider
{
    /**
     * 查询用户根据ID
     * @param $identifier
     * @return mixed
     */
    public function retrieveById($identifier);

    /**
     * 查询用户凭证
     * @param array $credentials
     * @return mixed
     */
    public function retrieveByCredentials(array $credentials);

    /**
     * 验证用户提供的凭证
     * @param Authenticatable $user
     * @param array $credentials
     * @return mixed
     */
    public function validateCredentials(Authenticatable $user,array $credentials);

    /**
     * 添加用户
     * @param array $params
     * @return mixed
     */
    public function createUser(array $params);
}