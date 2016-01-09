<?php

namespace Lucaszz\DoctrineDatabaseBackup\Storage;

interface Storage
{
    /**
     * @param $key
     * @param $value
     *
     * @throws \RuntimeException
     */
    public function put($key, $value);

    /**
     * @param $key
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function read($key);

    /**
     * @param $key
     *
     * @return bool
     */
    public function has($key);
}
