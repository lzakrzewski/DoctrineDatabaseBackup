<?php

namespace Lucaszz\DoctrineDatabaseBackup\Storage;

class LocalStorage implements Storage
{
    /** {@inheritdoc} */
    public function read($path)
    {
        if (false === $path = @file_get_contents($path)) {
            throw new \RuntimeException(sprintf('Unable to read file %s', $path));
        }

        return $path;
    }

    /** {@inheritdoc} */
    public function put($path, $contents)
    {
        if (false === @file_put_contents($path, $contents)) {
            throw new \RuntimeException(sprintf('Unable to write file %s', $path));
        }
    }

    /** {@inheritdoc} */
    public function has($string)
    {
        return file_exists($string);
    }
}
