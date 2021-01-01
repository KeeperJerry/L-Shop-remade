<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Frontend\Character;

use app\Handlers\Api\User\CloakHandler;
use app\Http\Controllers\Controller;
use app\Services\Auth\Exceptions\UserDoesNotExistException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class CloakController
 * Responsible for displaying images of the cloak sides.
 */
class CloakController extends Controller
{
    /**
     * It is responsible for the output of the front side of the cloak image.
     *
     * @param Request      $request
     * @param CloakHandler $handler
     *
     * @return Response
     */
    public function front(Request $request, CloakHandler $handler): Response
    {
        try {
            $image = $handler->front($request->route('username'));

            // If cloak does not exists, return empty response.
            if ($image === null) {
                return new Response('');
            }

            return response($image->encode('png'), 200, [
                'Content-Type' => 'image/png'
            ]);
        } catch (UserDoesNotExistException $e) {
            return new Response('user_not_found', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * It is responsible for the output of the back side of the cloak image.
     *
     * @param Request      $request
     * @param CloakHandler $handler
     *
     * @return Response
     */
    public function back(Request $request, CloakHandler $handler): Response
    {
        try {
            $image = $handler->back($request->route('username'));

            // If cloak does not exists, return empty response.
            if ($image === null) {
                return new Response('');
            }

            return response($image->encode('png'), 200, [
                'Content-Type' => 'image/png'
            ]);
        } catch (UserDoesNotExistException $e) {
            return new Response('user_not_found', Response::HTTP_NOT_FOUND);
        }
    }
}
