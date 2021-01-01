<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Admin\Users;

use app\DataTransferObjects\Admin\Users\Edit\AddBan;
use app\DataTransferObjects\Admin\Users\Edit\Edit;
use app\DataTransferObjects\Admin\Users\Edit\PaginationList;
use app\Exceptions\Ban\BanNotFoundException;
use app\Exceptions\InvalidArgumentException;
use app\Exceptions\Media\Character\InvalidRatioException;
use app\Exceptions\User\UserNotFoundException;
use app\Handlers\Admin\Users\Edit\AddBanHandler;
use app\Handlers\Admin\Users\Edit\CartHandler;
use app\Handlers\Admin\Users\Edit\DeleteBanHandler;
use app\Handlers\Admin\Users\Edit\DeleteCloakHandler;
use app\Handlers\Admin\Users\Edit\DeleteSkinHandler;
use app\Handlers\Admin\Users\Edit\EditHandler;
use app\Handlers\Admin\Users\Edit\PurchasesHandler;
use app\Handlers\Admin\Users\Edit\RenderHandler;
use app\Handlers\Admin\Users\Edit\UploadCloakHandler;
use app\Handlers\Admin\Users\Edit\UploadSkinHandler;
use app\Http\Controllers\Controller;
use app\Http\Requests\Admin\Users\Edit\AddBanRequest;
use app\Http\Requests\Admin\Users\Edit\EditRequest;
use app\Http\Requests\Admin\Users\Edit\UploadSkinCloakRequest;
use app\Services\Auth\Exceptions\EmailAlreadyExistsException;
use app\Services\Auth\Exceptions\UsernameAlreadyExistsException;
use app\Services\Auth\Permissions;
use app\Services\Notification\Notifications\Error;
use app\Services\Notification\Notifications\Info;
use app\Services\Notification\Notifications\Success;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function app\permission_middleware;

class EditController extends Controller
{
    public function __construct()
    {
        $this->middleware(permission_middleware(Permissions::ADMIN_USERS_CRUD_ACCESS));
        $this->middleware(permission_middleware(Permissions::ADMIN_PURCHASES_ACCESS))->only('purchases');
        $this->middleware(permission_middleware(Permissions::ADMIN_GAME_CART_ACCESS))->only('cart');
    }

    public function render(Request $request, RenderHandler $handler): JsonResponse
    {
        try {
            return new JsonResponse(Status::SUCCESS, $handler->handle((int)$request->route('user')));
        } catch (UserNotFoundException $e) {
            throw new NotFoundHttpException();
        }
    }

    public function edit(EditRequest $request, EditHandler $handler): JsonResponse
    {
        $dto = (new Edit())
            ->setUserId((int)$request->route('user'))
            ->setUsername($request->get('username'))
            ->setEmail($request->get('email'))
            ->setPassword($request->get('password'))
            ->setBalance((float)$request->get('balance'))
            ->setRoles($request->get('roles'))
            ->setPermissions($request->get('permissions'));

        try {
            $handler->handle($dto);

            return (new JsonResponse(Status::SUCCESS))
                ->addNotification(new Success(__('msg.admin.users.edit.success')));
        } catch (UserNotFoundException $e) {
            return (new JsonResponse('user_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.admin.users.edit.user_not_found')));
        } catch (UsernameAlreadyExistsException $e) {
            return (new JsonResponse('username_already_exists'))
                ->setHttpStatus(Response::HTTP_CONFLICT)
                ->addNotification(new Error(__('msg.admin.users.edit.username_already_exists')));
        } catch (EmailAlreadyExistsException $e) {
            return (new JsonResponse('email_already_exists'))
                ->setHttpStatus(Response::HTTP_CONFLICT)
                ->addNotification(new Error(__('msg.admin.users.edit.email_already_exists')));
        }
    }

    public function uploadSkin(UploadSkinCloakRequest $request, UploadSkinHandler $handler): JsonResponse
    {
        try {
            $handler->handle((int)$request->route('user'), $request->file('file'));

            return (new JsonResponse(Status::SUCCESS))
                ->addNotification(new Success(__('msg.frontend.profile.skin.success')));
        } catch (UserNotFoundException $e) {
            return (new JsonResponse('user_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.user_not_found')));
        } catch (InvalidRatioException $e) {
            return (new JsonResponse('invalid_ratio'))
                ->setHttpStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->addNotification(new Error(__('msg.frontend.profile.skin.invalid_ratio')));
        }
    }

    public function uploadCloak(UploadSkinCloakRequest $request, UploadCloakHandler $handler): JsonResponse
    {
        try {
            $handler->handle((int)$request->route('user'), $request->file('file'));

            return (new JsonResponse(Status::SUCCESS))
                ->addNotification(new Success(__('msg.frontend.profile.cloak.success')));
        } catch (UserNotFoundException $e) {
            return (new JsonResponse('user_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.user_not_found')));
        } catch (InvalidRatioException $e) {
            return (new JsonResponse('invalid_ratio'))
                ->setHttpStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->addNotification(new Error(__('msg.frontend.profile.cloak.invalid_ratio')));
        }
    }

    public function deleteSkin(Request $request, DeleteSkinHandler $handler): JsonResponse
    {
        try {
            if ($handler->handle((int)$request->route('user'))) {
                return (new JsonResponse(Status::SUCCESS))
                    ->addNotification(new Info(__('msg.frontend.profile.skin.delete.success')));
            }

            return (new JsonResponse(Status::FAILURE))
                ->setHttpStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addNotification(new Error(__('msg.frontend.profile.skin.delete.fail')));
        } catch (UserNotFoundException $e) {
            return (new JsonResponse('user_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.user_not_found')));
        }
    }

    public function deleteCloak(Request $request, DeleteCloakHandler $handler): JsonResponse
    {
        try {
            if ($handler->handle((int)$request->route('user'))) {
                return (new JsonResponse(Status::SUCCESS))
                    ->addNotification(new Info(__('msg.frontend.profile.cloak.delete.success')));
            }

            return (new JsonResponse(Status::FAILURE))
                ->addNotification(new Error(__('msg.frontend.profile.cloak.delete.fail')));
        } catch (UserNotFoundException $e) {
            return (new JsonResponse('user_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.user_not_found')));
        }
    }

    public function addBan(AddBanRequest $request, AddBanHandler $handler): JsonResponse
    {
        $dto = (new AddBan())
            ->setUserId((int)$request->route('user'))
            ->setMode($request->get('mode'))
            ->setForever((bool)$request->get('forever'))
            ->setDateTime($request->get('date_time'))
            ->setDays((int)$request->get('days'))
            ->setReason($request->get('reason'));

        try {
            $ban = $handler->handle($dto);

            return (new JsonResponse(Status::SUCCESS, [
                'ban' => $ban
            ]))
                ->addNotification(new Info(__('msg.admin.users.edit.ban.add.success')));
        } catch (UserNotFoundException $e) {
            return (new JsonResponse('user_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.admin.users.edit.ban.add.user_not_found')));
        } catch (InvalidArgumentException $e) {
            return (new JsonResponse('date_time_empty'))
                ->setHttpStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->addNotification(new Error(__('validation.required', [
                    'attribute' => __('content.admin.users.edit.actions.add_ban.DateTime')
                ])));
        }
    }

    public function deleteBan(Request $request, DeleteBanHandler $handler): JsonResponse
    {
        try {
            $handler->handle((int)$request->route('ban'));

            return (new JsonResponse(Status::SUCCESS))
                ->addNotification(new Info(__('msg.admin.users.edit.ban.delete.success')));
        } catch (BanNotFoundException $e) {
            return (new JsonResponse('ban_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.admin.users.edit.ban.delete.not_found')));
        }
    }

    public function purchases(Request $request, PurchasesHandler $handler): JsonResponse
    {
        $dto = new PaginationList();
        $dto
            ->setUserId((int)$request->route('user'))
            ->setPage((int)$request->get('page'))
            ->setPerPage((int)$request->get('per_page'))
            ->setOrderBy($request->get('order_by'))
            ->setDescending((bool)$request->get('descending'));


        return new JsonResponse(Status::SUCCESS, $handler->handle($dto));
    }

    public function cart(Request $request, CartHandler $handler): JsonResponse
    {
        $dto = new PaginationList();
        $dto
            ->setUserId((int)$request->route('user'))
            ->setPage((int)$request->get('page'))
            ->setPerPage((int)$request->get('per_page'))
            ->setOrderBy($request->get('order_by'))
            ->setDescending((bool)$request->get('descending'));

        return new JsonResponse(Status::SUCCESS, $handler->handle($dto));
    }
}
