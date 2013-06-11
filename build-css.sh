#!/bin/bash

cd /var/www/mattrude.com/wp-content/themes/kirby && \
rm -rf ../twentyeleven/style.less && \
cp ../twentyeleven/style.css ../twentyeleven/style.less && \
lessc style.less > style.css && \
cat ../../plugins/jetpack/modules/widgets/widget-grid-and-list.css >> style.css && \
cat ../../plugins/jetpack/modules/widgets/widgets.css >> style.css && \
cat ../../plugins/jetpack/modules/sharedaddy/sharing.css >> style.css && \
cat ../../plugins/mp6/components/responsive/css/admin-bar.css >> style.css && \
rm -f style.css.gz && \
java -jar /root/bin/web/yuicompressor-2.4.8pre.jar style.css > style.min.css && \
gzip style.min.css && \
mv style.min.css.gz style.css.gz && \
mkdir images && \
cp -R ../twentyeleven/images/* images/
