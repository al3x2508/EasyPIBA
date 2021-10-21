#!/bin/sh
find . -type d \( -path ./vendor -o -path ./node_modules \) -prune -false -o -iname "*.php" | xargs xgettext --language=php --add-comments=NOTE --keyword=__:1,2 --keyword=__ --from-code=UTF-8 -o i18n.pot
find . -name '*.po' | xargs -I{} msgmerge -U {} i18n.pot