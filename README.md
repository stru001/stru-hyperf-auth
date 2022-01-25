# stru-hyperf-auth

#### Description
```
类似实现laravel auth,仅实现了session登录验证，其他方式可自行实现
```


#### Installation
```
composer require stru/stru-hyperf-auth
```

#### Publish
```
php bin/hyperf.php vendor:publish hyperf/session
php bin/hyperf.php vendor:publish stru/stru-hyperf-auth
```

#### Database
```
php bin/hyperf.php migrate
```

#### Config
```
// config/authload/middlewares.php 添加session
\Hyperf\Session\Middleware\SessionMiddleware::class,
\Hyperf\Validation\Middleware\ValidationMiddleware::class,

// config/authload/exceptions.php 添加异常处理
\Stru\StruHyperfAuth\AuthExceptionHandler::class,

// 添加模型 App\Model\User.php  [数据库在上面设置中通过migrate发布]
1. 如果要实现注册在模型下添加
protected $fillable = ['name','account','email','password','mobile'];
2. 要使用验证 需实现接口 Authenticatable，（use Stru\StruHyperfAuth\Authenticatable;）
3. User模型中添加如下代码
public function getAuthIdentifierName(): string
{
    return $this->getKeyName();
}

public function getAuthIdentifier()
{
    return $this->getKey();
}

public function getAuthPassword(): string
{
    return $this->password;
}
```

#### Use
```
// 在使用登录注册视图之前要先安装 “stru/stru-hyperf-ui” 具体使用方法详见其文档，同时要取消如下注释
1. resources/views/layouts/app.blade.php
    38，45，48，58 行注释取消
2. resources/views/home.blade.php  
    69，71，74 行的注释取消  (如果无需使用该模板，则无需此操作)

// 添加控制器（LoginController），在控制器编写如下代码
<?php


namespace App\Controller\Auth;

use Hyperf\Contract\ContainerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Stru\StruHyperfAuth\AuthManager;
use function Hyperf\ViewEngine\view;

/**
 * Class LoginController
 * @package App\Controller\Auth
 * @Controller(prefix="auth")
 */
class LoginController
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;
    /**
     * @Inject
     * @var ResponseInterface
     */
    protected $response;
    /**
     * @Inject
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;
    /**
     * @Inject
     * @var AuthManager
     */
    protected $auth;
    /**
     * @RequestMapping(path="login",methods="GET")
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * @RequestMapping(path="login",methods="POST")
     */
    public function login()
    {
        if ($errMessage = $this->validateLogin()){
            return view('auth.login',[
                'error_message' => $errMessage
            ]);
        }
        $email = $this->request->input('email','');
        $password = $this->request->input('password','');

        if(! $this->auth->guard()->attempt(['email' => $email,'password' =>$password]))
        {
            return view('auth.login',[
                'error_message' => '用户不存在或密码错误'
            ]);
        }
        return $this->response->redirect('/home');
    }
    /**
     * @RequestMapping(path="register",methods="GET")
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * @RequestMapping(path="register",methods="POST")
     */
    public function register()
    {
        if ($errMessage = $this->validateRegister()){
            return view('auth.register',[
                'error_message' => $errMessage
            ]);
        }
        $params = $this->request->all();
        if (! $this->auth->guard()->checkRegister($params)){
            return view('auth.register',[
                'error_message' => '邮箱|账号|手机号 已注册或系统异常'
            ]);
        }
        return $this->response->redirect('/auth/login');
    }
    /**
     * @RequestMapping(path="logout",methods="POST")
     */
    public function logout()
    {
        $this->auth->guard()->logout();
        return $this->response->redirect('/auth/login');
    }

    private function validateLogin()
    {
        $validator = $this->validationFactory->make(
            $this->request->all(),
            [
                'email' => 'required|email',
                'password' => 'required|string'
            ]
        );
        if ($validator->fails()){
            return $validator->errors()->first();
        }
        return null;
    }

    protected function validateRegister()
    {
        $validator = $this->validationFactory->make(
            $this->request->all(),
            [
                'name' => 'required|string|max:50',
                'email' => 'required|email|max:50',
                'password' => 'required|string',
                'password_confirmation' => 'required|string|same:password'
            ],
            [
                'name.max' => '用户名最大支持50位',
                'email.max' => '邮箱最大支持50位',
                'password_confirmation.same' => '两次密码不一致',
            ]
        );
        if ($validator->fails()){
            return $validator->errors()->first();
        }
        return null;
    }
}
```


