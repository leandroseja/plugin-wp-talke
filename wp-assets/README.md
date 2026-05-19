# Assets pro diretório WordPress.org

Esses arquivos NÃO vão dentro do ZIP do plugin. Eles vão na pasta `assets/` do SVN do diretório WP.org, separados do código.

## O que precisa ter aqui antes de submeter

- `banner-1544x500.png` — banner principal da página do plugin (1544×500)
- `banner-772x250.png` — banner pra retina/mobile (772×250)
- `icon-256x256.png` — cópia do icon (pode reusar `../assets/icon-256x256.png`)
- `icon-128x128.png` — ícone menor pra listagens (128×128)
- `screenshot-1.png` — tela do plugin no admin (max 1280×800)
- `screenshot-2.png` — fluxo "Conectar com Talke CRM"
- `screenshot-3.png` — exemplo de lead capturado no CRM

## Como subir pro SVN

Após aprovação inicial no WP.org, você ganha acesso a `https://plugins.svn.wordpress.org/talke-crm/`. Os assets vão na pasta `assets/` da raiz desse SVN (NÃO em `trunk/`):

```
svn co https://plugins.svn.wordpress.org/talke-crm/
cd talke-crm
cp ../leadsaas/plugin_wp/wp-assets/*.png assets/
svn add assets/*
svn commit -m "Update assets"
```

O GitHub Action (`.github/workflows/release-wp-plugin.yml`) usa a 10up action que automaticamente copia esses assets do diretório `wp-assets/` quando o nome do diretório está configurado — se quiser, posso ajustar o YAML pra apontar pra essa pasta.
