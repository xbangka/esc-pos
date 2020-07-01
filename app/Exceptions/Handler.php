<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {   
        $code   = $exception->getCode();
        $msg    = $exception->getMessage();
        $file   = $exception->getFile();
        $line   = $exception->getLine();
        $err    = [];
        
        if($code!=200){
            $err['code']    = $code ;
            $err['message'] = $msg ;
            if(config('app.debug')){
                $err['file']    = $file ;
                $err['line']    = $line ;
            }

            return response($err)->header('Content-Type', 'application/json');
        }
        return parent::render($request, $exception);
    }
}
