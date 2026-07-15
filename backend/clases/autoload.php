<?php
spl_autoload_register(function ($class_name) {
    $paths = [
        __DIR__ . '/' . $class_name . '.php',
        __DIR__ . '/../enums/' . $class_name . '.php'
    ];
    
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
?>
