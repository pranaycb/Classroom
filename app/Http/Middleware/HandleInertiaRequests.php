<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use App\Models\Classroom;
use App\Action\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),

            'status' => $request->session()->get('status'),
            'success' => $request->session()->get('success'),
            'error' => $request->session()->get('error'),

            ...(!Auth::check() ? [] : [
                'theme' => Storage::json('colors.json'),
                'auth' => [
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'profile' => $user->profile_path,
                    ],
                ],
            ]),

            'ziggy' => fn() => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ];
    }
}
