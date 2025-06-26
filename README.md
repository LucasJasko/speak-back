# Projet Alert MNS

Ce projet est réalisé dans le cadre de la formation MNS développeur web et web mobile.

Pour fonctionner ce projet doit avoir un serveur apache avec les modules suivant actif:

- Headers_module
- Rewrite_module
- ssl_module modules/mod_ssl.so

- Include conf/extra/httpd-ssl.conf

le virtualhost doit inclure (pas obligatoire car configuration pas au point pour l'instant):
SSLEngine on
SSLCertificateFile "${SRVROOT}/conf/key/server.crt"
SSLCertificateKeyFile "${SRVROOT}/conf/key/server.key"

et la directive suivante activé dans le httpd.conf pour obtenir la variable "REMOTE_HOST" dans la variable serveur:

- HostnameLookups On

Pour CRUD des fichiers dans le projet, le serveur apache doit avoir les droits pour le faire dans le dossier contenant le projet. Pour se faire, sous fedora la commande est:

- sudo setfacl -R -d -m u:apache:rwx /chemin/vers/le/projet

Il nécessite aussi les programmes tiers suivant:

- Composer
- OpenSSL
- Perl
- wscat

Et le serveur socket doit être activé avec la commande bash:

php {chemin_jusqu'a_la_racine_du_projet}/class/Src/SocketServer.php

OU
se positionner dans ~/class/Src puis:

php SocketServer.php

On peut aussi vérifier les connexions actives sur le socket avec la commande suivante une fois le serveur lancé:

netstat -ano | findstr :8060

8060 ou autre port si différent
