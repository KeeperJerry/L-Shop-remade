<?php
/** @noinspection PhpStrictTypeCheckingInspection */
declare(strict_types = 1);

namespace app\Providers;

use app\Exceptions\UnexpectedValueException;
use app\Repository\Server\ServerRepository;
use app\Services\Caching\CachingRepository;
use app\Services\Caching\IlluminateCachingRepository;
use app\Services\Cart\Storage\Session as SessionCartStorage;
use app\Services\Cart\Storage\Storage as ClassStorage;
use app\Services\Database\GarbageCollection\DoctrineGarbageCollector;
use app\Services\Database\GarbageCollection\GarbageCollector;
use app\Services\Database\Transaction\DoctrineTransactor;
use app\Services\Database\Transaction\Transactor;
use app\Services\Database\Transfer\Pool;
use app\Services\Database\Transfer\Queries\Version050a\MySQLQuery;
use app\Services\Database\Transfer\Version050ATransfer;
use app\Services\DateTime\Formatting\Formatter;
use app\Services\DateTime\Formatting\HumanizeFormatter;
use app\Services\Item\Image\Hashing\Hasher;
use app\Services\Item\Image\Hashing\MD5Hasher;
use app\Services\Media\Character\Cloak\Applicators\Applicator as CloakApplicator;
use app\Services\Media\Character\Cloak\Applicators\DefaultApplicator as DefaultCloakApplicator;
use app\Services\Media\Character\Skin\Applicators\Applicator as SkinApplicator;
use app\Services\Media\Character\Skin\Applicators\DefaultApplicator as DefaultSkinApplicator;
use app\Services\Monitoring\Drivers\Driver as MonitoringDriver;
use app\Services\Monitoring\Drivers\RconDriver as RconMonitoring;
use app\Services\Monitoring\Drivers\RconResponseParser;
use app\Services\Monitoring\Monitoring;
use app\Services\Notification\Drivers\Driver;
use app\Services\Notification\Drivers\Session;
use app\Services\Security\Captcha\Captcha;
use app\Services\Security\Captcha\ReCaptcha;
use app\Services\Server\Persistence\Storage\Session as SessionPersistenceStorage;
use app\Services\Server\Persistence\Storage\Storage as PersistenceStorage;
use app\Services\Settings\DataType;
use app\Services\Settings\DefaultSettings;
use app\Services\Settings\Settings;
use app\Services\Url\Signing\Signer;
use app\Services\Url\Signing\Validator;
use D3lph1\MinecraftRconManager\Connector;
use D3lph1\MinecraftRconManager\DefaultConnector;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Driver::class, Session::class);
        $this->app->singleton(PersistenceStorage::class, SessionPersistenceStorage::class);
        $this->app->singleton(ClassStorage::class, SessionCartStorage::class);
        $this->app->singleton(Formatter::class, HumanizeFormatter::class);
        $this->app->singleton(Settings::class, DefaultSettings::class);
        $this->app->bind(SkinApplicator::class, DefaultSkinApplicator::class);
        $this->app->bind(CloakApplicator::class, DefaultCloakApplicator::class);
        $this->app->singleton(Captcha::class, function (Application $app) {
            $settings = $app->make(Settings::class);

            return new ReCaptcha(
                $settings->get('system.security.captcha.recaptcha.public_key')->getValue(),
                $settings->get('system.security.captcha.recaptcha.secret_key')->getValue()
            );
        });
        $this->app->singleton(Hasher::class, MD5Hasher::class);

        $this->app->singleton(CachingRepository::class, IlluminateCachingRepository::class);

        $this->app->singleton(MonitoringDriver::class, function (Application $app) {
            $driverClass = $app->make(Repository::class)->get('monitoring.driver');
            $driver = $app->make($driverClass);
            if (!($driver instanceof MonitoringDriver)) {
                throw new UnexpectedValueException(
                    'Monitoring driver must be implements ' . MonitoringDriver::class . ' interface'
                );
            }

            return $driver;
        });

        $this->app->singleton(Connector::class, DefaultConnector::class);

        $this->app->singleton(RconMonitoring::class, function (Application $app) {
            $settings = $app->make(Settings::class);

            return new RconMonitoring(
                $app->make(Connector::class),
                $settings->get('system.monitoring.rcon.command')->getValue(),
                $settings->get('system.monitoring.rcon.timeout')->getValue(DataType::INT),
                $app->make(RconResponseParser::class, [
                    'pattern' => $settings->get('system.monitoring.rcon.pattern')->getValue()
                ])
            );
        });

        $this->app->singleton(Monitoring::class, function (Application $app) {
            return new Monitoring(
                $app->make(ServerRepository::class),
                $app->make(MonitoringDriver::class),
                $app->make(CachingRepository::class),
                $app->make(LoggerInterface::class),
                $app->make(Settings::class)->get('system.monitoring.rcon.ttl')->getValue(DataType::FLOAT)
            );
        });

        $this->app->singleton(Signer::class, function (Application $app) {
            $settings = $app->make(Settings::class);

            return new Signer(
                $settings->get('api.algorithm')->getValue(),
                $settings->get('api.key')->getValue(),
                $settings->get('api.delimiter')->getValue()
            );
        });

        $this->app->singleton(Validator::class, function (Application $app) {
            $settings = $app->make(Settings::class);

            return new Validator(
                $settings->get('api.algorithm')->getValue(),
                $settings->get('api.key')->getValue(),
                $settings->get('api.delimiter')->getValue()
            );
        });

        $this->app->singleton(Pool::class, function (Application $app) {
            $pool = new Pool();
            $pool->put(Version050ATransfer::VERSION, new Version050ATransfer(
                $app->make(EntityManagerInterface::class),
                $app->make(MySQLQuery::class)
            ));

            return $pool;
        });

        $this->app->singleton(GarbageCollector::class, DoctrineGarbageCollector::class);
        $this->app->singleton(Transactor::class, DoctrineTransactor::class);
    }
}
