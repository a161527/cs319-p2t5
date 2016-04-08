<?php

namespace App\Exceptions;

use Exception;
use File;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        //There should really be a better way to get this into json,
        //but this works as well
        if ($e instanceof ValidationException) {
            $message = "[";
            $doneOne = false;
            foreach ($e->validator->messages()->all() as $err) {
                if (!$doneOne) {
                    $doneOne = true;
                } else {
                    $message = $message . ",";
                }
                $message = $message . $err . "\n";
            }
            $message = $message . "]";
            return response($message, 400)->header("Content-Type", "application/json");
        } else if ($e instanceof HttpException) {
            if ($e->getStatusCode() == 404) {
                return response(File::get(public_path() . '/index.html'));
            }
            return $this->renderHttpException($e);
        }

        return parent::render($request, $e);
    }
}
