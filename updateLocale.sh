#!/bin/sh
find . -iname "*.php" | xargs xgettext --add-comments=NOTE --keyword=__:1,2 --keyword=__ --from-code=UTF-8 -o i18n.pot
find . -name '*.po' | xargs -I{} msgmerge -U {} i18n.pot
find . -name '*.po' | xargs -I{} msgfmt {} -o i18n.mo