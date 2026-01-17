<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

/**
 * HandleInertiaRequests Middleware
 *
 * Handles Inertia.js requests and responses for the Vue.js frontend.
 * Manages shared data between server and client, including flash messages
 * and session data. Configures the root template and asset versioning
 * for the single-page application experience.
 *
 * @author Laravel Assessment System
 *
 * @version 1.0.0
 */
class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * Specifies the Blade template that serves as the base HTML
     * structure for the Inertia.js application. This template
     * contains the Vue.js mounting point and initial page data.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * Used by Inertia.js to determine when client-side assets
     * need to be refreshed. Returns the parent implementation
     * which typically uses file modification times or build hashes.
     *
     * @see https://inertiajs.com/asset-versioning
     *
     * @param  Request  $request  The current HTTP request
     * @return string|null The current asset version identifier
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * Shares common data across all Inertia.js pages including
     * flash messages for notifications and session data for
     * maintaining candidate information throughout the assessment.
     * Uses lazy evaluation to optimize performance.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @param  Request  $request  The current HTTP request
     * @return array<string, mixed> Shared props for all pages
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),

            // Share flash messages for "Upload Success" notifications
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],

            // Share session data so Vue header can always see the candidate name
            'session' => [
                'candidate_name' => fn () => $request->session()->get('candidate_name'),
                'candidate_email' => fn () => $request->session()->get('candidate_email'),
            ],
        ];
    }
}
