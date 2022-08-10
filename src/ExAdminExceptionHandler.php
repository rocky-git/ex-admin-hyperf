<?php
namespace ExAdmin\hyperf;
use ExAdmin\hyperf\exception\HttpResponseException;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Hyperf\Di\Annotation\Inject;
class ExAdminExceptionHandler extends  ExceptionHandler
{
    public function handle(\Throwable $throwable, ResponseInterface $response)
    {
        // 判断被捕获到的异常是希望被捕获的异常
        if ($throwable instanceof HttpResponseException) {
            $response = $throwable->getResponse();
            // 阻止异常冒泡
            $this->stopPropagation();
            return $response;
        }
        // 交给下一个异常处理器
        return $response;
        // 或者不做处理直接屏蔽异常
    }

    /**
     * 判断该异常处理器是否要对该异常进行处理
     */
    public function isValid(\Throwable $throwable): bool
    {
        return true;
    }
}