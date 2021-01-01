<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Frontend\Auth;

use app\Http\Controllers\Controller;
use app\Services\Auth\Auth;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;

/**
 * Class LogoutController
 * Handles requests related to logout user.
 */
class LogoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Processes the user's logon request.
     *
     * @param Auth $auth
     *
     * @return JsonResponse
     */
    public function handle(Auth $auth): JsonResponse
    {
        $auth->logout();

        return new JsonResponse(Status::SUCCESS);
    }
}
