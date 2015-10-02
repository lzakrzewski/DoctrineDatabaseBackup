<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup;

class Filesystem
{
    /**
     * @param $dir
     *
     * @throws \RuntimeException
     */
    public function prepareDir($dir)
    {
        @mkdir($dir);

        if (false === file_exists($dir)) {
            throw new \RuntimeException(sprintf('Unable to create directory %s', $dir));
        }

        $this->cleanUp($dir);
    }

    /**
     * @param string $source
     * @param string $destination
     *
     * @throws \RuntimeException
     */
    public function copy($source, $destination)
    {
        if (false === file_exists($source)) {
            throw new \RuntimeException(sprintf("Source file '%s' does not exist.", $source));
        }

        if (!copy($source, $destination)) {
            throw new \RuntimeException(sprintf("Unable to copy '%s' to '%s'", $source, $destination));
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

    private function cleanUp($dir)
    {
        foreach (scandir($dir) as $file) {
            $filePath = $dir.'/'.$file;

            if (!is_file($filePath)) {
                continue;
            }

            unlink($filePath);
        }
    }
}
