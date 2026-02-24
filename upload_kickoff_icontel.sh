#!/bin/bash
# Script para subir kickoff_icontel al servidor FTP

HOST="ftp.icontel.cl"
USER="icontel"
PASS="q_#(0R06MzEx"
REMOTE_DIR="/public_html/intranet/kickoff_icontel"
LOCAL_DIR="/Users/octavioaranedaojeda/Documents/Diseño/Clientes/iConTel/intranet/kickoff_icontel"

echo "🚀 Subiendo kickoff_icontel al servidor..."

# Usar lftp para subir recursivamente
lftp -c "
set ftp:ssl-allow no;
open -u $USER,$PASS $HOST;
mirror -R --verbose $LOCAL_DIR $REMOTE_DIR;
bye
"

echo "✅ Subida completada"
