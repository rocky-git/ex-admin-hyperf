<?php

namespace ExAdmin\hyperf\middleware;

use ExAdmin\ui\Route;
use ExAdmin\ui\support\Request;
use Hyperf\Utils\ApplicationContext;
use Psr\Http\Server\MiddlewareInterface;
use Hyperf\Utils\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\HeaderBag;

class RequestMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        //设置request
        Request::init(function (\Symfony\Component\HttpFoundation\Request $q) use($request){
            $files = [];
            foreach ($request->getUploadedFiles() as $key=>$file){
                $files[$key] = new UploadedFile($file->getPathname(),$file->getClientFilename(),$file->getMimeType(),$file->getError());
            };
            $q->initialize($request->getQueryParams(),$request->getParsedBody(),[],$request->getCookieParams(),$files,$request->getServerParams(),$request->getBody()->getContents());
            $q->headers = new HeaderBag($request->getHeaders());
            $q->setMethod($request->getMethod());
        });
        Route::setObjectParamAfter(function ($class){
            if(ApplicationContext::getContainer()->has($class)){
                return ApplicationContext::getContainer()->get($class);
            }
        });
        return $handler->handle($request);
    }
}