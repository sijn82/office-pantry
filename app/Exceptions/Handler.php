<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Illuminate\Auth\AuthenticationException;
use Auth;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }

    // protected function whoopsHandler()
    // {
    //     try {
    //         return app(\Whoops\Handler\HandlerInterface::class);
    //     } catch (\Illuminate\Contracts\Container\BindingResolutionException $e) {
    //         return parent::whoopsHandler();
    //     }
    // }
    
    
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // dd($request);
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        if ($request->is('office') || $request->is('office/*' || $request->is('register/office'))) {
            return redirect()->guest('/login/office');
        }
        
        if (    $request->is('api/export-picklists') 
            ||  $request->is('api/export-picklists-full')
            ||  $request->is('api/export-routing')
            ||  $request->is('api/export-companies')        
        ) {
            return redirect()->guest('/login/office');
        }

        if (    $request->is('warehouse') 
            ||  $request->is('warehouse/*') 
            ||  $request->is('register/warehouse')
        ) {
            return redirect()->guest('/login/warehouse');
        }

        return redirect()->guest(route('login'));
    }
}
