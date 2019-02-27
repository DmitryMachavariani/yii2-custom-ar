#!/usr/bin/env bash
DIR="$( cd "$( dirname $( dirname $( dirname "${BASH_SOURCE[0]}" ) ) )" >/dev/null && pwd )/tools/ngrok/"
SITE_URL=`cat ${DIR}domain`

CMD="${DIR}ngrok http -host-header=rewrite ${SITE_URL}:80"
eval $CMD

#https://113530ee.ngrok.io
#https://api.telegram.org/bot634300623:AAHS_y1HbYfSKc1ufW5enDhkVeX6_xWUox4/setWebhook?url=https://050c24c9.ngrok.io/bot
#https://api.telegram.org/bot647016009:AAHITMzXBeYfHE2L-1MR23JF18O9N86rpL0/setWebhook?url=https://telegram.poradom.ru/bot