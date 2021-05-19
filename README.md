# patotools
Class, traits and some tools i use !

## host file

127.0.0.1 barrecode.pat

## VirtualHost
'''
<VirtualHost *:80>
    ServerAdmin patotools@barrecode.pat
    DocumentRoot "D:/LocalWebSites/patobeur/barrecode/public"
    ServerName barrecode.pat
    ServerAlias www.barrecode.pat
    <Directory "D:/LocalWebSites/patobeur/barrecode/public">
        Order allow,deny
        Allow from all
        Require all granted
    </Directory>
</VirtualHost>
'''