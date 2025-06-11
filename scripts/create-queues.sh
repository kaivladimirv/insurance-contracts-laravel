#!/usr/bin/env bash

set -e

# Используем значения из переменных окружения или задаём значения по умолчанию
RABBIT_HOST="${RABBITMQ_HOST:-rabbitmq}"
RABBIT_PORT="${RABBITMQ_API_PORT:-15672}" # порт API (не 5672)
RABBIT_USER="${RABBITMQ_USER:-guest}"
RABBIT_PASS="${RABBITMQ_PASSWORD:-guest}"
VHOST="$(python3 -c "import urllib.parse; print(urllib.parse.quote('''${RABBITMQ_VHOST:-/}''', safe=''))")"

# Проверка QUEUE_NAMES
if [ -z "$QUEUE_NAMES" ]; then
  echo "Environment variable QUEUE_NAMES is not set"
  exit 1
fi

IFS=',' read -ra QUEUES <<< "$QUEUE_NAMES"

for QUEUE in "${QUEUES[@]}"; do
  HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" \
    -u "$RABBIT_USER:$RABBIT_PASS" \
    -H "content-type:application/json" \
    -X PUT "http://$RABBIT_HOST:$RABBIT_PORT/api/queues/$VHOST/$QUEUE" \
    -d '{"durable":true}')

  if [[ "$HTTP_CODE" = "201" || "$HTTP_CODE" = "204" ]]; then
    echo "Queue '$QUEUE' has been created"
  else
    echo "Error creating queue '$QUEUE' (HTTP $HTTP_CODE)"
  fi
done
