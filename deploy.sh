#!/bin/sh

echo "Connexion au serveur FTP..."
DEPLOY_FOLDER="/home/hestiamaelcorp/web/preview.maelcorp.com/public_html/${CI_PROJECT_NAME}_${CI_PIPELINE_ID}/"
echo "Dossier cible sur le FTP: $DEPLOY_FOLDER"

lftp -u "hestiamaelcorp,MaelCorpHestiaCP2024@!" preview.maelcorp.com <<EOF
set ssl:verify-certificate no
mkdir -p $DEPLOY_FOLDER
mirror --verbose -R --exclude .git --exclude node_modules --exclude-glob "*.log" --exclude deploy.sh ./ $DEPLOY_FOLDER
bye
EOF

echo "Déploiement terminé !"