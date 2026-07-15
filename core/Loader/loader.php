<?php

function buildFileRegistry(string $root):array {
    $registry = [];

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            $root,
            RecursiveDirectoryIterator::SKIP_DOTS
        )
    );

    $root = realpath($root);

    foreach ($iterator as $file) {

        if (!$file->isFile()) {
            continue;
        }

        if ($file->getExtension() !== "php") {
            continue;
        }

        $fullPath = $file->getRealPath();

        $relative = substr($fullPath, strlen($root) + 1);
        $relative = preg_replace('/\.php$/', '', $relative);
        $relative = str_replace("\\", "/", $relative);

        if (isset($registry[$relative])) {
            throw new Exception("Duplicate file identifier '{$relative}'");
        }

        $registry[$relative] = $fullPath;
    }

    return $registry;
}

function &getFileRegistry(string $projectRoot):array {
    static $registry = null;

    if ($registry === null) {
        $registry = buildFileRegistry($projectRoot);
    }

    return $registry;
}

function requireFile(string|array $files, string $projectRoot){
    $registry = &getFileRegistry($projectRoot);

    foreach ((array)$files as $file) {
        $file = trim(str_replace("\\", "/", $file), "/");

        if (!isset($registry[$file])) {
            return buildResponse("File '{$file}.php' not found.", 404);
        }

        require_once $registry[$file];
    }

    return true;
}

?>