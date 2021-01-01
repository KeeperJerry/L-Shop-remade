<?php
declare(strict_types=1);

namespace app\Http\Controllers\Frontend\Profile;

use app\Exceptions\Media\Character\InvalidRatioException;
use app\Exceptions\Media\Character\InvalidResolutionException;
use app\Handlers\Frontend\Profile\Character\DeleteCloakHandler;
use app\Handlers\Frontend\Profile\Character\DeleteSkinHandler;
use app\Handlers\Frontend\Profile\Character\UploadCloakHandler;
use app\Handlers\Frontend\Profile\Character\UploadSkinHandler;
use app\Handlers\Frontend\Profile\Character\VisitHandler;
use app\Http\Controllers\Controller;
use app\Http\Requests\Frontend\Profile\Character\UploadCloakRequest;
use app\Http\Requests\Frontend\Profile\Character\UploadSkinRequest;
use app\Services\Auth\Auth;
use app\Services\Media\Character\Cloak\Accessor as CloakAccessor;
use app\Services\Media\Character\Skin\Accessor as SkinAccessor;
use app\Services\Notification\Notifications\Error;
use app\Services\Notification\Notifications\Info;
use app\Services\Notification\Notifications\Success;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use app\Services\Security\Accessors\Frontend\Profile\CharacterAccessor;
use app\Services\Settings\DataType;
use app\Services\Settings\Settings;
use function app\accessor_middleware;

/**
 * Class CharacterController
 * Responsible for handling requests from the character's page.
 */
class CharacterController extends Controller
{
    public function __construct(Auth $auth, SkinAccessor $skinAccessor, CloakAccessor $cloakAccessor)
    {
        $this->middleware(accessor_middleware(CharacterAccessor::class))->only('render');
    }

    /**
     * Returns the data to render the page with the character.
     *
     * @param Auth         $auth
     * @param Settings     $settings
     * @param VisitHandler $handler
     *
     * @return JsonResponse
     */
    public function render(
        Auth $auth,
        Settings $settings,
        VisitHandler $handler): JsonResponse
    {
        $dto = $handler->handle();
        $username = $auth->getUser()->getUsername();

        $skinImageSizes = [];
        foreach ($dto->getAvailableSkinImageSizes() as $item) {
            $skinImageSizes[] = "{$item[0]}x{$item[1]}";
        }
        $cloakImageSizes = [];
        foreach ($dto->getAvailableCloakImageSizes() as $item) {
            $cloakImageSizes[] = "{$item[0]}x{$item[1]}";
        }

        return new JsonResponse(Status::SUCCESS, [
            'skin' => [
                'allowed' => $dto->isAllowSetSkin(),
                'front' => route('api.skin.front', ['username' => $username]),
                'back' => route('api.skin.back', ['username' => $username]),
                'max_file_size' => $settings->get('system.profile.character.skin.max_file_size')->getValue(DataType::FLOAT),
                'image_sizes' => implode(', ', $skinImageSizes),
                'default' => $dto->isSkinDefault(),
            ],
            'cloak' => [
                'allowed' => $dto->isAllowSetCloak(),
                'front' => route('api.cloak.front', ['username' => $username]),
                'back' => route('api.cloak.back', ['username' => $username]),
                'max_file_size' => $settings->get('system.profile.character.cloak.max_file_size')->getValue(DataType::FLOAT),
                'image_sizes' => implode(', ', $cloakImageSizes),
                'exists' => $dto->isCloakExists(),
            ]
        ]);
    }

    /**
     * Handles the request to load the user skin image.
     *
     * @param UploadSkinRequest $request
     * @param UploadSkinHandler $handler
     *
     * @return JsonResponse
     */
    public function uploadSkin(UploadSkinRequest $request, UploadSkinHandler $handler): JsonResponse
    {
        try {
            $handler->handle($request->file('file'));
        } catch (InvalidRatioException $e) {
            return (new JsonResponse('invalid_ration'))
                ->addNotification(new Error(__('msg.frontend.profile.skin.invalid_ratio')));
        } catch (InvalidResolutionException $e) {
            return (new JsonResponse('invalid_resolution'))
                ->addNotification(new Error(__('msg.frontend.profile.skin.invalid_resolution')));
        }

        return (new JsonResponse(Status::SUCCESS))
            ->addNotification(new Success(__('msg.frontend.profile.skin.success')));
    }

    /**
     * Handles the request to load the user cloak image.
     *
     * @param UploadCloakRequest $request
     * @param UploadCloakHandler $handler
     *
     * @return JsonResponse
     */
    public function uploadCloak(UploadCloakRequest $request, UploadCloakHandler $handler): JsonResponse
    {
        try {
            $handler->handle($request->file('file'));
        } catch (InvalidRatioException $e) {
            return (new JsonResponse('invalid_ration'))
                ->addNotification(new Error(__('msg.frontend.profile.cloak.invalid_ratio')));
        } catch (InvalidResolutionException $e) {
            return (new JsonResponse('invalid_resolution'))
                ->addNotification(new Error(__('msg.frontend.profile.cloak.invalid_resolution')));
        }

        return (new JsonResponse(Status::SUCCESS))
            ->addNotification(new Success(__('msg.frontend.profile.cloak.success')));
    }

    /**
     * Handles the request to delete the user skin image.
     *
     * @param DeleteSkinHandler $handler
     *
     * @return JsonResponse
     */
    public function deleteSkin(DeleteSkinHandler $handler): JsonResponse
    {
        if ($handler->handle()) {
            return (new JsonResponse(Status::SUCCESS))
                ->addNotification(new Info(__('msg.frontend.profile.skin.delete.success')));
        }

        return (new JsonResponse(Status::FAILURE))
            ->addNotification(new Error(__('msg.frontend.profile.skin.delete.fail')));
    }

    /**
     * Handles the request to delete the user cloak image.
     *
     * @param DeleteCloakHandler $handler
     *
     * @return JsonResponse
     */
    public function deleteCloak(DeleteCloakHandler $handler): JsonResponse
    {
        if ($handler->handle()) {
            return (new JsonResponse(Status::SUCCESS))
                ->addNotification(new Info(__('msg.frontend.profile.cloak.delete.success')));
        }

        return (new JsonResponse(Status::FAILURE))
            ->addNotification(new Error(__('msg.frontend.profile.cloak.delete.fail')));
    }
}
