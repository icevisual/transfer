<?php
namespace App\Exceptions;

use App\Services\ServiceLog;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Validation\ValidationException;


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
        \App\Exceptions\ServiceException::class
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $e            
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request            
     * @param \Exception $e            
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        \App\Http\Controllers\BaseController::setHeader();
    	/**
    	 * Handle Service Exceptions
    	 */
        if ($e instanceof \App\Exceptions\ServiceException) {

            $response = array(
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            );
            ServiceLog::requestLog($request, $response);
            return \Response::json($response);
        }
        
        if (! \Config::get('app.debug')) {
            if ($e instanceof ModelNotFoundException) {
                $e = new NotFoundHttpException($e->getMessage(), $e);
            }
            if ($e instanceof \Illuminate\Contracts\Container\BindingResolutionException) {
                $response = array(
                    'status' => 604,
                    'message' => '相应服务没有启动'
                );
            } elseif ($e instanceof \Symfony\Component\Debug\Exception\FatalErrorException) {
                $response = array(
                    'status' => 605,
                    'message' => 'method wrong'
                );
            } else {
                
                $response = array(
                    'status' => $e->getCode() ?  : 606,
                    'message' => $e->getMessage() ?  : '请检查请求数据'
                );
            }
    		ServiceLog::errorLog($request,$response);
    		return \Response::json($response);
    	}
        return parent::render($request, $e);
    }
}
