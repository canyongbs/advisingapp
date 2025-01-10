#!/bin/sh

QUEUE_NAME=$1
QUEUE_ENV_VAR=$2

for run in $(seq "$TOTAL_QUEUE_WORKERS"); do
# if [ $run -eq 1 ]; then
#     SLEEP_TIME=0
# else
#     SLEEP_TIME=3;
# fi
SLEEP_TIME=3;

cp -r "/tmp/s6-overlay-templates/laravel-queue/service" "/etc/s6-overlay/s6-rc.d/$QUEUE_NAME-queue-$run";
sed -i -e "s/VAR_QUEUE/$QUEUE_ENV_VAR/g" "/etc/s6-overlay/s6-rc.d/$QUEUE_NAME-queue-$run/run";
sed -i -e "s/TEMPLATE_SLEEP/$SLEEP_TIME/g" "/etc/s6-overlay/s6-rc.d/$QUEUE_NAME-queue-$run/run";
cp "/tmp/s6-overlay-templates/laravel-queue/laravel-queue" "/etc/s6-overlay/s6-rc.d/user/contents.d/$QUEUE_NAME-queue-$run";
done