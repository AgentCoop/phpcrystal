#!/bin/bash
# fail script immediately on any errors in external commands
set -e

composer update --prefer-dist --no-interaction --prefer-stable --no-suggest