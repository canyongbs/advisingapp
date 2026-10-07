#!/bin/sh
set -eu

ANNOUNCE_IP="$(hostname -i | awk '{print $1}')"

# Written by cluster-init-entrypoint.sh; cleared so a restarted container is not healthy early.
rm -f /tmp/redis-cluster-ready

server_pid=''
init_pid=''

cleanup() {
    trap - EXIT
    if [ -n "$init_pid" ]; then
        kill -TERM "$init_pid" 2>/dev/null || true
        wait "$init_pid" 2>/dev/null || true
    fi
    if [ -n "$server_pid" ]; then
        kill -TERM "$server_pid" 2>/dev/null || true
        wait "$server_pid" 2>/dev/null || true
    fi
}

trap cleanup EXIT
trap 'exit 0' TERM INT

redis-server \
    --cluster-enabled yes \
    --cluster-config-file /data/nodes.conf \
    --cluster-node-timeout 5000 \
    --cluster-announce-ip "$ANNOUNCE_IP" \
    --appendonly no \
    --save '' &
server_pid=$!

if [ "$#" -gt 0 ]; then
    (
        sh /usr/local/bin/cluster-init-entrypoint.sh "$@" || {
            exit_code=$?
            kill -TERM "$server_pid" 2>/dev/null || true
            exit "$exit_code"
        }
    ) &
    init_pid=$!
fi

wait "$server_pid"
if [ -n "$init_pid" ]; then
    wait "$init_pid"
fi
