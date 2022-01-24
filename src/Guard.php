<?php

namespace Stru\StruHyperfAuth;


interface Guard
{
    /**
     * 当前用户身份验证
     * @return mixed
     */
    public function check();

    /**
     * 判断当前用户是来宾
     * @return mixed
     */
    public function guest();

    /**
     * 获取用户
     * @return mixed
     */
    public function user();

    /**
     * 获取当前用户ID
     * @return mixed
     */
    public function id();

    /**
     * 校验用户凭证（username,password）
     * @param array $credentials
     * @return mixed
     */
    public function validate(array $credentials =[]);

    /**
     * 设置当前用户
     * @param Authenticatable $user
     * @return mixed
     */
    public function setUser(Authenticatable $user);

    /**
     * 处理登录
     * @param array $credentials
     * @return mixed
     */
    public function attempt(array $credentials = []);

    /**
     * 登录
     * @param Authenticatable $user
     * @return mixed
     */
    public function login(Authenticatable $user);

    /**
     * 退出
     * @return mixed
     */
    public function logout();
}