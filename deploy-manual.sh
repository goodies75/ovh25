#!/bin/bash

echo "🚀 Script de déploiement manuel pour OVH"
echo "========================================"

# Informations de connexion
SSH_HOST="ssh.cluster023.hosting.ovh.net"
SSH_USER="opetitcorq"
SSH_PORT="22"
REMOTE_PATH="/home/opetitcorq/www"

echo "📡 Connexion SSH à OVH..."
echo "Host: $SSH_HOST"
echo "User: $SSH_USER"
echo "Path: $REMOTE_PATH"
echo ""

# Commands à exécuter sur le serveur
cat << 'EOF' > /tmp/deploy_commands.sh
# Aller dans le répertoire web
cd /home/opetitcorq/www

# Vérifier si Git est initialisé
if [ ! -d ".git" ]; then
    echo "🔧 Initialisation du repository Git..."
    git init
    git remote add origin https://github.com/goodies75/ovh25.git
fi

# Récupérer la dernière version
echo "📥 Récupération des fichiers depuis GitHub..."
git fetch origin main
git reset --hard origin/main

# Copier les fichiers de déploiement
echo "📁 Copie des fichiers de déploiement..."
cp -r deploy/* ./

# Vérification
echo "✅ Fichiers déployés:"
ls -la

echo "🎉 Déploiement terminé avec succès!"
EOF

echo "📋 Commandes à exécuter sur le serveur:"
echo "========================================"
cat /tmp/deploy_commands.sh
echo ""
echo "💡 Pour vous connecter manuellement:"
echo "ssh $SSH_USER@$SSH_HOST -p $SSH_PORT"
