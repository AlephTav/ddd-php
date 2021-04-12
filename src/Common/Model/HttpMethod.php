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

    public function isGet(): bool
    {
        return $this->constant === 'GET';
    }

    public function isHead(): bool
    {
        return $this->constant === 'HEAD';
    }

    public function isPost(): bool
    {
        return $this->constant === 'POST';
    }

    public function isPut(): bool
    {
        return $this->constant === 'PUT';
    }

    public function isDelete(): bool
    {
        return $this->constant === 'DELETE';
    }

    public function isConnect(): bool
    {
        return $this->constant === 'CONNECT';
    }

    public function isOptions(): bool
    {
        return $this->constant === 'OPTIONS';
    }

    public function isTrace(): bool
    {
        return $this->constant === 'TRACE';
    }

    public function isPatch(): bool
    {
        return $this->constant === 'PATCH';
    }

    public function hasBody(): bool
    {
        return $this->isPost() || $this->isPut() || $this->isPatch();
    }
}