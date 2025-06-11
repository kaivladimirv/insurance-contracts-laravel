#!/usr/bin/env bash
set -e

# Подгружаем .env в текущую сессию
if [ -f .env ]; then
    export $(grep -v '^#' .env | xargs)
else
    echo "❌ .env file not found"
    exit 1
fi

if [ -z "$QUEUE_NAMES" ]; then
    echo "Environment variable QUEUE_NAMES is not set"
    exit 1
fi

bash ./scripts/create-queues.sh

php artisan queue:work -v --tries=3 --queue="$QUEUE_NAMES"
