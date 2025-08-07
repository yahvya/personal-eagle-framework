<?php

namespace Tests\SaboCore\TestUtils;

class MockPhpInput
{
    public static string $body = '';
    public static mixed $context = null;
    private int $position = 0;

    public function stream_open($path, $mode, $options, &$opened_path): bool
    {
        $this->position = 0;
        return true;
    }

    public function stream_read($count): string
    {
        $result = substr(string: self::$body, offset: $this->position,length: $count);
        $this->position += strlen($result);
        return $result;
    }

    public function stream_eof(): bool
    {
        return $this->position >= strlen(string: self::$body);
    }

    public function stream_stat(): array
    {
        return [];
    }

    public function stream_seek(int $offset, int $whence = SEEK_SET): bool
    {
        $length = strlen(string: self::$body);

        switch ($whence) {
            case SEEK_SET:
                $this->position = $offset;
                break;
            case SEEK_CUR:
                $this->position += $offset;
                break;
            case SEEK_END:
                $this->position = $length + $offset;
                break;
            default:
                return false;
        }

        return $this->position >= 0 && $this->position <= $length;
    }

    public function stream_tell(): int
    {
        return $this->position;
    }

    public function stream_close(): void
    {
    }
}
