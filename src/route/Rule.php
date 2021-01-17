<?php

namespace think\annotation\route;

abstract class Rule
{
    /**
     * @var string|array
     */
    public array|string $middleware;

    /**
     * 后缀
     * @var string
     */
    public string $ext;

    /**
     * @var string
     */
    public string $denyExt;

    /**
     * @var bool
     */
    public bool $https;

    /**
     * @var string
     */
    public string $domain;

    /**
     * @var bool
     */
    public bool $completeMatch;

    /**
     * @var string|array
     */
    public array|string $cache;

    /**
     * @var bool
     */
    public bool $ajax;

    /**
     * @var bool
     */
    public bool $pjax;

    /**
     * @var bool
     */
    public bool $json;

    /**
     * @var array
     */
    public array $filter;

    /**
     * @var array
     */
    public array $append;

    public function getOptions()
    {
        return array_intersect_key(get_object_vars($this), array_flip([
            'middleware', 'ext', 'deny_ext', 'https', 'domain', 'complete_match', 'cache', 'ajax', 'pjax', 'json', 'filter', 'append',
        ]));
    }

}
