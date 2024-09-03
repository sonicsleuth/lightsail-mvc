<?php
/*
 * Helpers, as the name suggests, help you with tasks.
 * Each helper file is simply a collection of functions in a particular category.
 */

/**
 * @param $paths
 * @param $data
 * @return mixed
 *
 * Extends the View by loading another file relative the base Views Path.
 */
function extend_view(array $paths, array $data): void
{
    // Extract data array to variables.
    extract($data);

    foreach ($paths as $path) {
        $fullPath = VIEWS_PATH . $path . '.php';
        if (file_exists($fullPath)) {
            include $fullPath;
        } else {
            trigger_error("View file not found: $fullPath", E_USER_WARNING);
        }
    }
}


/**
 * @param $paths
 *
 * Loads Javascripts.
 */
function load_script(array $paths): void
{
    foreach ($paths as $path) {
        $scriptPath = htmlspecialchars(WEB_ROOT . 'js/' . $path . '.js', ENT_QUOTES, 'UTF-8');
        echo "<script src=\"$scriptPath\"></script>\r\n";
    }
}


/**
 * @param $paths
 * Loads CSS Styles
 */
function load_style(array $paths): void
{
    foreach ($paths as $path) {
        $stylePath = htmlspecialchars(WEB_ROOT . 'css/' . $path . '.css', ENT_QUOTES, 'UTF-8');
        echo "<link rel=\"stylesheet\" href=\"$stylePath\" type=\"text/css\" />\r\n";
    }
}
