<?php

namespace AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Infrastructure\Enums\AbstractEnum;

/**
 * @method static static GET(string $method = null)
 * @method static static HEAD(string $method = null)
 * @method static static POST(string $method = null)
 * @method static static PUT(string $method = null)
 * @method static static DELETE(string $method = null)
 * @method static static CONNECT(string $method = null)
 * @method static static OPTIONS(string $method = null)
 * @method static static TRACE(string $method = null)
 * @method static static PATCH(string $method = null)
 */
class HttpMethod extends AbstractEnum
{
    private const GET = null;
    private const HEAD = null;
    private const POST = null;
    private const PUT = null;
    private const DELETE = null;
    private const CONNECT = null;
    private const OPTIONS = null;
    private const TRACE = null;
    private const PATCH = null;
}