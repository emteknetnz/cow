#!/bin/bash

if [ ! -z "$DEBUG_COW" ] ; then
    DIR="$COW_DIR";
else
    DIR="/app";
fi

DIR="$(readlink -f $DIR)";

if [[ ! -d "$DIR/vendor" ]] ; then
    cd $DIR;
    composer install --prefer-dist -vv;
    cd -;
fi

if [ ! -z "$TEST_COW" ] ; then
    cd $DIR;
    ./vendor/bin/phpunit $@;
    exit;
fi

if [ ! -z "$PHPCS_COW" ] ; then
    cd $DIR;
    ./vendor/bin/phpcs --standard=PSR12 bin/ src/ tests/ $@;
    exit;
fi

$DIR/bin/cow $@