#!/bin/bash

echo "============================================"
echo "   MVola - Deploiement local (CodeIgniter 4)"
echo "============================================"
echo ""

# Aller dans le dossier du script
cd "$(dirname "$0")"

# --- 1. Verification de PHP ---
if ! command -v php &> /dev/null
then
    echo "[ERREUR] PHP n'a pas ete trouve."
    echo "Installez PHP avec Homebrew : brew install php"
    exit 1
fi

echo "[OK] PHP detecte :"
php -v | head -n 1
echo ""

# --- 2. Installation des dependances Composer ---
if [ ! -f "vendor/autoload.php" ]
then
    echo "[INFO] Dossier vendor absent, installation des dependances..."

    if ! command -v composer &> /dev/null
    then
        echo "[ERREUR] Composer n'a pas ete trouve."
        echo "Installez Composer : brew install composer"
        exit 1
    fi

    composer install --no-interaction

    if [ $? -ne 0 ]
    then
        echo "[ERREUR] L'installation Composer a echoue."
        exit 1
    fi

else
    echo "[OK] Dependances Composer deja installees."
fi

echo ""

# --- 3. Fichier .env ---
if [ ! -f ".env" ]
then
    echo "[INFO] Creation du fichier .env..."

    cat > .env << EOF
CI_ENVIRONMENT = development
EOF

else
    echo "[OK] Fichier .env present."
fi

echo ""

# --- 4. Preparation SQLite ---
if [ ! -d "writable" ]
then
    mkdir writable
fi

echo "[INFO] Application des migrations SQLite..."

php spark migrate --all

if [ $? -ne 0 ]
then
    echo "[ATTENTION] Les migrations ont rencontre un souci (peut-etre deja a jour)."
fi

echo ""

echo "[INFO] Execution des seeders (types d'operation)..."

php spark db:seed TypeSeeder

echo ""

# --- 5. Lancement serveur ---
HOST="localhost"
PORT="8080"

echo "============================================"
echo "   Demarrage du serveur :"
echo "   http://$HOST:$PORT/"
echo "   CTRL+C pour arreter"
echo "============================================"
echo ""

# Ouvrir automatiquement le navigateur
open "http://$HOST:$PORT/"

# Lancer CodeIgniter
php spark serve --host $HOST --port $PORT

echo ""

read -p "Appuyez sur Entree pour fermer..."