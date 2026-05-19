#!/usr/bin/env bash
set -euo pipefail

# Build script: gera o ZIP do plugin pronto pra upload no WP.org.
# Roda a partir da pasta plugin_wp/ ou da raiz do repo.

PLUGIN_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$PLUGIN_DIR"

DIST_DIR="dist"
PLUGIN_SLUG="talke-crm"

rm -rf "$DIST_DIR"
mkdir -p "$DIST_DIR/$PLUGIN_SLUG"

cp -R \
  talke-crm.php \
  readme.txt \
  LICENSE \
  uninstall.php \
  assets \
  src \
  languages \
  "$DIST_DIR/$PLUGIN_SLUG/"

cd "$DIST_DIR"
zip -rq "$PLUGIN_SLUG.zip" "$PLUGIN_SLUG/" -x "**/.DS_Store"

echo "ZIP gerado em $PLUGIN_DIR/$DIST_DIR/$PLUGIN_SLUG.zip"
ls -lh "$PLUGIN_SLUG.zip"
