<?php


namespace Stru\StruHyperfAuth\Provider;

use Stru\StruHyperfAuth\Arrayable;
use Stru\StruHyperfAuth\Authenticatable;
use Stru\StruHyperfAuth\UserProvider;

class EloquentUserProvider implements UserProvider
{

    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function retrieveById($identifier)
    {
        $model = $this->createModel();

        return $this->newModelQuery($model)
                    ->where($model->getAuthIdentifireName(),$identifier)
                    ->first();
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
            (count($credentials) === 1 &&
                self::contains($this->firstCredentialKey($credentials),'password'))){
            return null;
        }

        $query = $this->newModelQuery();

        foreach ($credentials as $key => $value) {
            if (self::contains($key, 'password')) {
                continue;
            }

            if (is_array($value) || $value instanceof Arrayable) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'];

        return password_verify($plain, $user->getAuthPassword());
    }

    protected function createModel()
    {
        $class = '\\'.ltrim($this->model,'\\');
        return new $class;
    }

    protected function newModelQuery($model = null)
    {
        return is_null($model)
                    ? $this->createModel()->newQuery()
                    : $model->newQuery();
    }

    public function getModel()
    {
        return $this->model;
    }
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    protected function firstCredentialKey(array $credentials)
    {
        foreach ($credentials as $key => $value) {
            return $key;
        }
    }

    public static function contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }


    public function createUser(array $data)
    {
        $query = $this->newModelQuery();
        $user = $query->where('account', $data['account'])
            ->orWhere('email', $data['email'])
            ->orWhere('mobile', $data['mobile'])
            ->first();
        if ($user){
            return false;
        }
        $model = $this->createModel();
        $model->create($data);
        return true;
    }
}