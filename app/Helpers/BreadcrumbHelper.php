<?php

use Illuminate\Support\Facades\Route;

if (!function_exists('generateBreadcrumbs')) {
    function generateBreadcrumbs()
    {
        $routeName = Route::currentRouteName(); // e.g. "ranks.index"
        $params = Route::current()->parameters();

        $parts = explode('.', $routeName);

        // 👇 Always prepend "settings"
        array_unshift($parts, 'settings');

        $breadcrumbs = [];
        $url = '';

        foreach ($parts as $i => $part) {
            if ($i == 0) {
                // First part: settings
                $url = route('settings');
                $label = ucfirst($part);
            } else {
                // Build the route name progressively
                $routeKey = implode('.', array_slice($parts, 1, $i)); // remove "settings"
                $url = $i < count($parts) - 1 && Route::has($routeKey . '.index')
                    ? route($routeKey . '.index', $params)
                    : '';

                $label = match ($part) {
                    'index' => 'List',
                    'create' => 'Create',
                    'edit' => 'Edit',
                    default => ucfirst($part),
                };
            }

            $breadcrumbs[] = ['label' => $label, 'url' => $url];
        }

        return $breadcrumbs;
    }
}
if (!function_exists('generateBreadcrumbs_auto')) {
    function generateBreadcrumbs_auto()
    {
        $routeName = Route::currentRouteName(); // e.g. "appointmanager.create"
        $params    = Route::current()->parameters();

        $parts = explode('.', $routeName);

        $breadcrumbs = [];
        $url = '';

        foreach ($parts as $i => $part) {
            // Build the route progressively: "appointmanager.index", "appointmanager.create"
            $routeKey = implode('.', array_slice($parts, 0, $i + 1));

            // If the route exists, generate a link
            $url = $i < count($parts) - 1 && Route::has($routeKey)
                ? route($routeKey, $params)
                : '';

            // Convert part name to readable label
            $label = match ($part) {
                'index'  => 'List',
                'create' => 'Create',
                'edit'   => 'Edit',
                'show'   => 'View',
                default  => ucfirst($part),
            };

            $breadcrumbs[] = ['label' => $label, 'url' => $url];
        }

        return $breadcrumbs;
    }
}
