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
php bin/hyperf.php vendor:publish stru/stru-hyperf-auth
```

#### Config
```
// config/authload/middlewares.php 添加session
\Hyperf\Session\Middleware\SessionMiddleware::class,

// config/authload/exceptions.php 添加异常处理
\Stru\StruHyperfAuth\AuthExceptionHandler::class,

// App\Model\User.php
1. 实现接口 Authenticatable
2. 添加代码
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
// 正常添加控制器（LoginController），在控制器中加入如下方法
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
 * @return mixed
 * @RequestMapping(path="login",methods="GET")
 */
public function showLogin()
{
    return view('auth.login',[]);
}

/**
 * @return mixed
 * @RequestMapping(path="login",methods="POST")
 */
public function login()
{

    if ($errMessage = $this->validateLogin()){
        return $this->response->json([$errMessage]);
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
 * @return mixed
 * @RequestMapping(path="register",methods="GET")
 */
public function showRegister()
{
    return view('auth.register');
}

/**
 * @return mixed
 * @RequestMapping(path="register",methods="POST")
 */
public function register()
{
    return $this->request->all();
}

/**
 * @return mixed
 * @RequestMapping(path="logout",methods="POST")
 */
public function logout()
{
    $this->auth->guard('session')->logout();
    return $this->response->redirect('/auth/login');
}

public function guard()
{

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
```


