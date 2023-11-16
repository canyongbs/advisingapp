#!/bin/bash

read -r -d '' COPYRIGHT << EOM
Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.
EOM

insert()
{
    echo $1
    printf "$TAG"

    echo -e "$TAG\n\n$(cat $1)" > $1
}

replace()
{
    echo $1
}

blade()
{
#    echo $1

    START="{{--\n<copyright>"
    END="</copyright>\n--}}"
    TAG="$START\n$COPYRIGHT\n$END"
#    printf "$TAG";

    insert $1 $TAG
}

php()
{
    echo $1
}

js()
{
    echo $1
}

css()
{
    echo $1
}

vue()
{
    echo $1
}

handle()
{
#    echo $1;
    if [ "${1#*.}" == 'blade.php' ]; then
        blade $1
    else
        case ${1##*.} in
            php)
                php $1
                ;;
            js)
                js $1
                ;;
            css)
                css $1
                ;;
            vue)
                vue $1
                ;;
#            *)
#                echo $1
#                ;;
        esac
    fi
}

git add .
read -a FILES <<< `git ls-files`

#echo "${#FILES[@]}"

#FILE="widgets/form/tailwind.config.js"
#
#echo "${FILE%%.*}"
#
#echo "${FILE%.*}"
#
#echo "${FILE#*.}"
#
#echo "${FILE##*.}"

#for FILE in "${FILES[@]}"; do
##    echo "${FILE}"
##    echo "${FILE#*.}"
#    handle $FILE
#done

handle 'app-modules/assistant/resources/views/filament/pages/personal-assistant.blade.php'
