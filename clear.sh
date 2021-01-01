#!/bin/bash
php artisan cache:clear;
php artisan config:clear;
php artisan route:clear;
php artisan view:clear;
echo "[KeeperJerry]: Cache clear Completed!"