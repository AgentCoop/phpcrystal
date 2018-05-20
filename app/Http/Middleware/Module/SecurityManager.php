<?php

namespace App\Http\Middleware\Module;

use Closure;
use Auth;

use App\Services\PackageManager;
use App\Models\Physical\Repository\User;

use App\Component\Package\Annotation\SecurityPolicy;

class SecurityManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        /** @var PackageManager $packageManager */
        $packageManager = app()->make(PackageManager::class);

        $action = $request->route()->getAction();

        if (empty($action)) {
            throw new \RuntimeException(sprintf('Failed to retrieve routing action'));
        }

        list($controllerName, $methodName) = explode('@', $action['controller']);

        /** @var SecurityPolicy $securityPolicy */
        $securityPolicy = $packageManager->getAnnotationInstance($controllerName, $methodName, SecurityPolicy::class);
        $allowedRoles = $securityPolicy->getRoles();

        if (empty($allowedRoles)) {
            return $next($request);
        }

        if ( ! Auth::check()) {
            if ( ! empty($redirectTo = $securityPolicy->getNotAuthenticatedPage())) {
                return redirect($redirectTo);
            } else {
                return abort(401);
            }
        }

        $user = Auth::user();

        if ( ! in_array($user->getRole(), $allowedRoles)) {
            if ( ! empty($redirectTo = $securityPolicy->getNotAuthorizedPage())) {
                return redirect($redirectTo);
            } else {
                return abort(403);
            }
        }

        return $next($request);
    }
}
