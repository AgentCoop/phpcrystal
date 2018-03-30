#!/bin/bash
# fail script immediately on any errors in external commands
set -e

source travis_retry.sh

travis_retry composer update --prefer-dist --no-interaction --prefer-stable --no-suggest