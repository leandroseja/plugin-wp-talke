# Submissão do Talke CRM no WordPress.org

Este guia descreve os passos pra publicar o plugin no diretório oficial do WordPress.

## Pré-requisitos

- Conta no WordPress.org (mesma usada pra logins em wordpress.org/support).
- Backend do leadsaas com a rota `/integrations/wordpress/authorize` deployada em produção (Phase 1 do plano).
- ZIP do plugin gerado: `./scripts/build.sh` cria `dist/talke-crm.zip`.

## Passo 1 — Gerar o ZIP de submissão

```bash
cd plugin_wp
./scripts/build.sh
```

Confirma que o ZIP tem ~70KB e contém:
- `talke-crm.php`
- `readme.txt`
- `LICENSE`
- `uninstall.php`
- `assets/icon-256x256.png`
- `src/*.php` (Plugin, Settings, Connection, ScriptInjector, Api, TokenStore, Modules/*)
- `languages/talke-crm.pot`

## Passo 2 — Submeter

1. Acesse https://wordpress.org/plugins/developers/add/
2. Faz login com sua conta WP.org
3. Upload do `dist/talke-crm.zip`
4. Preenche descrição curta (a do `readme.txt` é o que aparece)

## Passo 3 — Aguardar aprovação

Revisão manual leva 2-8 semanas. O time pode pedir ajustes. Itens comuns que pedem:

- Toda saída deve usar `esc_html`, `esc_attr`, `esc_url` (já feito no Settings.php).
- Nenhuma chamada externa antes do user consentir explicitamente (já feito — só roda após "Conectar").
- `readme.txt` com seções no formato deles.
- Sem código ofuscado (já é fonte legível).

## Passo 4 — Pós-aprovação: configurar SVN

Após aprovação, você ganha acesso a `https://plugins.svn.wordpress.org/talke-crm/`. Suas credenciais SVN são suas credenciais wordpress.org.

Configurar secrets no GitHub:
1. Vai em Settings > Secrets and variables > Actions do repo `leadsaas`
2. Adiciona `WP_SVN_USERNAME` (seu username do WP.org)
3. Adiciona `WP_SVN_PASSWORD` (sua senha do WP.org)

## Passo 5 — Releases automatizados

Após config dos secrets, cada nova release sai via tag:

```bash
git tag wp-plugin-v1.0.1
git push origin wp-plugin-v1.0.1
```

O GitHub Action (`.github/workflows/release-wp-plugin.yml`) dispara, pega o conteúdo de `plugin_wp/`, sobe pra `trunk/` e tag correspondente no SVN. WP.org propaga a atualização em ~15min.

## Assets do diretório (banner, ícones, screenshots)

Esses NÃO ficam no ZIP do plugin — ficam em pasta `assets/` separada no SVN. Veja `wp-assets/README.md` pra detalhes.

## Passo 6 — Antes de cada bump de versão

Atualiza em 3 lugares:
- Header em `talke-crm.php`: `* Version: 1.0.1`
- `readme.txt`: `Stable tag: 1.0.1`
- Constante `TALKE_CRM_VERSION` em `talke-crm.php`
- Changelog em `readme.txt`

Commita, tag, push.
