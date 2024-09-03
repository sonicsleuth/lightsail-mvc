<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');

class Controller
{
    /**
     * @param $model
     * @return object|bool
     *
     * Load specified Model if the file exists.
     */
    protected function model(string $model): ?object
    {
        $modelFile = MODELS_PATH . $model . '.php';

        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        }

        return null;
    }


    /**
     * @param $view
     * @param array $data
     * @return bool
     *
     * Load specified View if the file exists.
     * The values of $data are available to the View as are the
     * index of each $data as its own variable.
     */
    protected function view(string $view, array $data = []): bool
    {
        $viewFile = VIEWS_PATH . $view . '.php';

        if (file_exists($viewFile)) {
            // Extract data array to variables.
            extract($data);
            require_once $viewFile;
            return true;
        }

        return false;
    }


    /**
     * @param array $files
     *
     * Load Helper files.
     *
     */
    protected function load_helper(array $files = []): void
    {
        array_walk($files, function($file) {
            $helperFile = HELPERS_PATH . $file . '.php';
            if (file_exists($helperFile)) {
                require_once $helperFile;
            } else {
                throw new \RuntimeException("Helper file not found: $helperFile");
            }
        });
    }

}