#!/bin/sh
set -eu

for endpoint in "$@"; do
    attempts=0
    until redis-cli -h "${endpoint%:*}" -p "${endpoint##*:}" ping 2>/dev/null | grep -q PONG; do
        attempts=$((attempts + 1))
        if [ "$attempts" -ge 60 ]; then
            echo "Redis node $endpoint did not become ready." >&2
            exit 1
        fi
        sleep 1
    done
done

expected_addresses=''
for endpoint in "$@"; do
    expected_addresses="$expected_addresses $(getent hosts "${endpoint%:*}" | awk '{print $1}' | head -n1):${endpoint##*:}"
done

# A node reports cluster_state:ok from persisted nodes.conf until its node-timeout expires, so also
# require every node to see every expected peer address as a live, connected link.
cluster_is_healthy() {
    for endpoint in "$@"; do
        redis-cli -h "${endpoint%:*}" -p "${endpoint##*:}" cluster info 2>/dev/null | grep -q 'cluster_state:ok' || return 1
        nodes="$(redis-cli -h "${endpoint%:*}" -p "${endpoint##*:}" cluster nodes 2>/dev/null)" || return 1
        for address in $expected_addresses; do
            echo "$nodes" | awk -v address="$address" '
                { split($2, parts, "[@,]") }
                parts[1] == address && $3 !~ /fail|handshake|noaddr/ && $8 == "connected" { found = 1 }
                END { exit found ? 0 : 1 }
            ' || return 1
        done
    done
}

seed="$1"
if ! redis-cli -h "${seed%:*}" -p "${seed##*:}" cluster info | grep -q 'cluster_slots_assigned:0'; then
    attempts=0
    until cluster_is_healthy "$@"; do
        attempts=$((attempts + 1))
        if [ "$attempts" -ge 20 ]; then
            break
        fi
        sleep 1
    done

    if cluster_is_healthy "$@"; then
        touch /tmp/redis-cluster-ready
        exit 0
    fi

    echo 'Redis cluster did not recover from persisted state; recreating it.' >&2
fi

for endpoint in "$@"; do
    redis-cli -h "${endpoint%:*}" -p "${endpoint##*:}" flushall >/dev/null 2>&1 || true
    redis-cli -h "${endpoint%:*}" -p "${endpoint##*:}" cluster reset hard >/dev/null 2>&1 || true
done

redis-cli --cluster create "$@" --cluster-replicas 0 --cluster-yes

attempts=0
until cluster_is_healthy "$@"; do
    attempts=$((attempts + 1))
    if [ "$attempts" -ge 60 ]; then
        echo 'Redis cluster did not become healthy.' >&2
        exit 1
    fi
    sleep 1
done

touch /tmp/redis-cluster-ready
