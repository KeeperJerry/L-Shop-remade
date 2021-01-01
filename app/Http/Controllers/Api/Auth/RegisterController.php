<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Api\Auth;

use app\Handlers\Api\Auth\RegisterHandler;
use app\Http\Controllers\Controller;
use app\Services\Auth\Exceptions\EmailAlreadyExistsException;
use app\Services\Auth\Exceptions\UsernameAlreadyExistsException;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function app\signed_middleware;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware(signed_middleware(['email', 'username', 'password', 'send_activation', 'authenticate']))
            ->only('register');
    }

    public function register(Request $request, RegisterHandler $handler): JsonResponse
    {
        try {
            $dto = $handler->handle(
                $request->get('username'),
                $request->get('email'),
                $request->get('password'),
                (bool)$request->get('send_activation'),
                (bool)$request->get('authenticate')
            );

            if ($dto->isSuccessfully()) {
                return new JsonResponse(Status::SUCCESS);
            } else {
                return (new JsonResponse(Status::FAILURE))
                    ->setHttpStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (UsernameAlreadyExistsException $e) {
            return (new JsonResponse('username_already_exists'))
                ->setHttpStatus(Response::HTTP_CONFLICT);
        } catch (EmailAlreadyExistsException $e) {
            return (new JsonResponse('email_already_exists'))
                ->setHttpStatus(Response::HTTP_CONFLICT);
        }
    }
}
