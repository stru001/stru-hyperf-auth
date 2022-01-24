<?php


namespace Stru\StruHyperfAuth;


interface Authenticatable
{
    /**
     * 获取用户唯一标识名称，email|username|mobile...
     * @return string
     */
    public function getAuthIdentifierName():string;

    /**
     * 获取用户唯一标识
     * @return mixed
     */
    public function getAuthIdentifier();

    /**
     * 获取用户密码
     * @return string
     */
    public function getAuthPassword():string;
}