<?php

namespace Storm\Drivers\Platforms\Development\Logging;

class DumpLogger implements ILogger {
    public function Log($Output) {
        echo var_dump($Output);
    }
}

?>