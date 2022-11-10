<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

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
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $throwable
     * @return void
     * @throws Exception
     */
    public function report(\Throwable $throwable)
    {
        parent::report($throwable);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $throwable
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     * @throws \Throwable
     */
    public function render($request, \Throwable $throwable)
    {
/*        if ($exception instanceof \Laravel\Nova\Exceptions\AuthenticationException) {
            return redirect('/');
        }*/

        if ($throwable instanceof \Illuminate\Foundation\Http\Exceptions\MaintenanceModeException) {
            return response()
                ->view('maintenance', [
                    'message' => trans('main.hint_maintenance_service_down')
                ], 503)
                ->header('Content-Type', 'text/html; charset=utf-8');
        }

/*        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(
                $this->getJsonMessage($throwable),
                $this->getExceptionHTTPStatusCode($throwable)
            );
        }*/

        if ($request->expectsJson()) {
            //return response - with error code
            if (!($throwable instanceof HttpResponseException)
                && !($throwable instanceof AuthenticationException)
                && !($throwable instanceof ValidationException)) {

                $message = $throwable->getMessage();
                $code = $throwable->getCode();
                return response()->json(['message' => $message], $code != 0 ? $code : 500);
            }
        }

        return parent::render($request, $throwable);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $guard = Arr::get($exception->guards(), 0);

        switch ($guard) {
            case 'admin':
                $login = 'admin/login';
                break;
            default:
                $login = 'login';
                break;
        }

        return redirect()->guest($login);
    }


    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
/*        $this->reportable(function (CustomException $e) {
        });*/
    }

    protected function getJsonMessage($e)
    {
        // You may add in the code, but it's duplication
        return [
            'status' => false,
            'message' => $e->getMessage()
        ];
    }

    protected function getExceptionHTTPStatusCode($e)
    {
        // Not all Exceptions have a http status code
        // We will give Error 500 if none found
        return method_exists($e, 'getStatusCode') ?
            $e->getStatusCode() : 500;
    }
}
