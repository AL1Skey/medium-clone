# List of Notes for Recreating this
- in laravel 11, kernel are not supported and to register the middleware, 
- There's no kernel.php so to add the middleware, use bootstrap/app.php
####  Laravel 10 or lower 
> app/Http/Kernel.php
  ```php
  <?php
    protected $middlewareGroups = [
    'web' => [
        // ... remove the comment to use middleware
        #\Spatie\Multitenancy\Http\Middleware\NeedsTenant::class,
        \Spatie\Multitenancy\Http\Middleware\EnsureValidTenantSession::class,
    ]
];
  ```
#### Laravel 11+
> bootstrap/app.php
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \Spatie\Multitenancy\Http\Middleware\NeedsTenant::class,
        \Spatie\Multitenancy\Http\Middleware\EnsureValidTenantSession::class,
    ]);
})
```
- In validate, the 'confirmed' in password are used to double check the password. the request body example is
```json
{
  "name": "John Doe",
  "email": "johndoe@example.com",
  "password": "mypassword123",
  "password_confirmation": "mypassword123"
}
```
And the example of validate
app/Http/Controllers/API/AuthController.php
```php
....
public function register(Request $request){
        // validation the requirements of request body
        $payload = $request->validate([
            "name" => "required|min:2|max:60",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:6|max:50|confirmed"
        ]);
...
}
.....
```
