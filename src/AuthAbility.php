<?php

declare(strict_types=1);
/**
 * This file is part of stru/stru-hyperf-auth.
 */
namespace Stru\StruHyperfAuth;

use Hyperf\Database\Model\Model;

/**
 * Trait AuthAbility.
 * @mixin Authenticatable|Model
 */
trait AuthAbility
{
    /**
     * 获取用户唯一标识名称，email|username|mobile...
     * @return string
     */
    public function getAuthIdentifierName():string
    {
        return $this->getKeyName();
    }

    /**
     * 获取用户唯一标识
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * 获取用户密码
     * @return string
     */
    public function getAuthPassword():string
    {
        return $this->password;
    }
}
