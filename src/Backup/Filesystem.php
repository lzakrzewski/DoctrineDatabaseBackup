<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup;

class Filesystem
{
    /**
     * @param string $path
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function read($path)
    {
        if (false === $path = @file_get_contents($path)) {
            throw new \RuntimeException(sprintf('Unable to read file %s', $path));
        }

        return $path;
    }

    /**
     * @param string $path
     *
     * @throws \RuntimeException
     *
     * @param $contents
     */
    public function write($path, $contents)
    {
        if (false === @file_put_contents($path, $contents)) {
            throw new \RuntimeException(sprintf('Unable to write file %s', $path));
        }
    }

    /**
     * @param $string
     *
     * @return bool
     */
    public function exists($string)
    {
        return file_exists($string);
    }
}
