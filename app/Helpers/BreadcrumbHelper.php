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

        foreach ($parts as $i => $part) {
            $isLast = $i === count($parts) - 1;

            // Try to build index route for first/main part
            if ($i === 0) {
                $baseRoute = $parts[0] . '.index'; // e.g. appointmanager.index
                $url = Route::has($baseRoute) ? route($baseRoute, $params) : '';
                $label = ucfirst($parts[0]);
            } else {
                // Use human-friendly labels
                $label = match ($part) {
                    'index'  => 'List',
                    'create' => 'Create',
                    'edit'   => 'Edit',
                    'show'   => 'View',
                    default  => ucfirst($part),
                };

                // Only make link if it's NOT the last part and route exists
                $routeKey = implode('.', array_slice($parts, 0, $i + 1));
                $url = (!$isLast && Route::has($routeKey)) ? route($routeKey, $params) : '';
            }

            $breadcrumbs[] = [
                'label' => $label,
                'url'   => $isLast ? '' : $url, // last item should not be clickable
            ];
        }

        return $breadcrumbs;
    }
}
