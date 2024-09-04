<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes\HeaderParameter;
use OpenApi\Attributes\Info;
use OpenApi\Attributes\PathItem;
use OpenApi\Attributes\Schema;
use ReflectionException;
use ReflectionMethod;

#[Info(
    version: '1.0.0',
    description: 'This API allows the user to work with insurance contracts.',
    title: 'Contracts Api'
)]
#[PathItem(
    path: '/api',
)]
#[HeaderParameter(
    name: 'X-Requested-With',
    schema: new Schema(type: 'string', default: 'XMLHttpRequest')
)]
class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * @throws ReflectionException
     */
    public function callAction($method, $parameters)
    {
        if ((new ReflectionMethod($this, $method))->isPublic()) {
            return parent::callAction($method, $parameters);
        }

        return $this->__call($method, $parameters);
    }
}
