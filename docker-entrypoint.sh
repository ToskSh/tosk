#!/bin/sh
set -e

cmd="$1"
shift

exec php bin/tosk $cmd "$@"