<?php

namespace App\Traits;

trait SystemAddonTrait
{
   /**
     * Get all published addons (Modules/{Module}/Addon).
     * @return array
     */
    public function get_addons(): array
    {
        $dir = base_path('Modules'); // absolute path
        $directories = self::getDirectories($dir);

        $addons = [];
        foreach ($directories as $directory) {
            $subDirs = self::getDirectories($dir . '/' . $directory);
            if (in_array('Addon', $subDirs, true)) {
                $addons[] = $dir . '/' . $directory;
            }
        }

        $result = [];
        foreach ($addons as $path) {
            $infoFile = $path . '/Addon/info.php';
            if (!is_file($infoFile)) {
                continue;
            }
            /** @var array $full_data */
            $full_data = include $infoFile;

            $result[] = [
                'addon_name'   => $full_data['name']         ?? '',
                'software_id'  => $full_data['software_id']  ?? '',
                'is_published' => $full_data['is_published'] ?? false,
            ];
        }

        return $result;
    }

    /**
     * Get admin routes from published addons.
     * @return array
     */
    public function get_addon_admin_routes(): array
    {
        $dir = base_path('Modules');

        $directories = self::getDirectories($dir);
        $addons = [];
        foreach ($directories as $directory) {
            $subDirs = self::getDirectories($dir . '/' . $directory);
            if (in_array('Addon', $subDirs, true)) {
                $addons[] = $dir . '/' . $directory;
            }
        }

        $routes = [];
        foreach ($addons as $path) {
            $infoFile = $path . '/Addon/info.php';
            $routesFile = $path . '/Addon/admin_routes.php';
            if (!is_file($infoFile) || !is_file($routesFile)) {
                continue;
            }

            /** @var array $info */
            $info = include $infoFile;
            if (!empty($info['is_published'])) {
                $routes[] = include $routesFile;
            }
        }

        return $routes;
    }

    /**
     * Check publish status for payment gateway addon (Modules/Gateways).
     * @return array
     */
    public function get_payment_publish_status(): array
    {
        $dir = base_path('Modules');

        $directories = self::getDirectories($dir);
        $gateways = [];
        foreach ($directories as $directory) {
            if ($directory !== 'Gateways') continue;

            $subDirs = self::getDirectories($dir . '/' . $directory);
            if (in_array('Addon', $subDirs, true)) {
                $gateways[] = $dir . '/' . $directory;
            }
        }

        $result = [];
        foreach ($gateways as $path) {
            $infoFile = $path . '/Addon/info.php';
            if (!is_file($infoFile)) {
                continue;
            }
            /** @var array $full_data */
            $full_data = include $infoFile;

            $result[] = [
                'is_published' => $full_data['is_published'] ?? false,
            ];
        }

        return $result;
    }

    /**
     * Safe directory lister.
     * @param string $path
     * @return array
     */
    function getDirectories(string $path): array
    {
        if (!is_dir($path)) {
            return [];
        }

        $items = scandir($path);
        if ($items === false) {
            return [];
        }

        $directories = [];
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $full = $path . '/' . $item;
            if (is_dir($full)) {
                $directories[] = $item;
            }
        }
        return $directories;
    }
}
