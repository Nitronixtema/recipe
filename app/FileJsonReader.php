<?php

namespace App;

class FileJsonReader
{
    protected bool $finished = false;
    protected $fp;
    protected int $i = 0;

    public function __construct(string $fileName)
    {
        $this->fp = fopen($fileName, 'r');
    }

    public function getItem(): ?array
    {
        $start = false;
        $str = '';

        while (false !== ($char = fgetc($this->fp))) {
            if ($start) {
                $str .= $char;
                if ($char === '}') {
                    if ($data = json_decode($str, true)) {
                        $this->i++;

                        return $data;
                    }
                }
            } else {
                if ($char === '{') {
                    $str = $char;
                    $start = true;
                }
            }
        }

        $this->finished = true;

        return null;
    }

    public function isOver(): bool
    {
        return $this->finished;
    }
}
