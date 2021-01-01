<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Api\Auth;

use app\Exceptions\ForbiddenException;
use app\Exceptions\User\UserNotFoundException;
use app\Handlers\Api\Auth\LoginHandler;
use app\Http\Controllers\Controller;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use function app\signed_middleware;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware(signed_middleware(['username', 'remember', 'without_checkpoints']))->only('login');
    }

    public function login(Request $request, LoginHandler $handler, Repository $config): Response
    {
        try {
            if ($handler->handle(
                $request->get('username'),
                (bool)$request->get('remember'),
                (bool)$request->get('without_checkpoints')
            )) {
                return new RedirectResponse($config->get('app.url'));
            }

            return new Response('', Response::HTTP_BAD_REQUEST);
        } catch (ForbiddenException $e) {
            return new Response('', Response::HTTP_FORBIDDEN);
        } catch (UserNotFoundException $e) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }
    }
}
