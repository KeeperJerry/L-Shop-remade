<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Servers;

use app\DataTransferObjects\Admin\Servers\DeleteResult;
use app\Events\Server\ServerWillBeDeletedEvent;
use app\Exceptions\Server\ServerNotFoundException;
use app\Repository\Server\ServerRepository;
use app\Services\Server\Persistence\Persistence;
use Illuminate\Contracts\Events\Dispatcher;

class DeleteHandler
{
    /**
     * @var ServerRepository
     */
    private $repository;

    /**
     * @var Persistence
     */
    private $persistence;

    /**
     * @var Dispatcher
     */
    private $eventDispatcher;

    public function __construct(ServerRepository $repository, Persistence $persistence, Dispatcher $eventDispatcher)
    {
        $this->repository = $repository;
        $this->persistence = $persistence;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param int $serverId
     *
     * @return DeleteResult
     * @throws ServerNotFoundException
     */
    public function handle(int $serverId): DeleteResult
    {
        $server = $this->repository->find($serverId);
        if ($server === null) {
            throw ServerNotFoundException::byId($serverId);
        }
        $persistentServer = $this->persistence->retrieve();
        $destroyPersistence = $persistentServer !== null && $persistentServer->getId() === $server->getId();
        if ($destroyPersistence) {
            $this->persistence->destroy();
        }

        $this->eventDispatcher->dispatch(new ServerWillBeDeletedEvent($server));
        $this->repository->remove($server);

        return new DeleteResult($destroyPersistence);
    }
}
