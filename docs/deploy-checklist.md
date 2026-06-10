# Deploy checklist

## Antes do deploy

- Configurar dominio final.
- Configurar `APP_URL` com HTTPS e dominio final.
- Garantir `APP_TIMEZONE=Europe/Lisbon`.
- Configurar MySQL: host, porta, base de dados, utilizador e password.
- Configurar `SITE_NAME`, `SITE_PHONE`, `SITE_WHATSAPP`, `SITE_EMAIL` e `SITE_LOCATION`.
- Configurar `SITE_DEFAULT_DESCRIPTION` e `SITE_DEFAULT_OG_IMAGE`.
- Configurar SMTP se forem usados emails reais.
- Garantir `APP_ENV=production`.
- Garantir `APP_DEBUG=false`.
- Correr `php artisan test` antes do deploy.
- Correr `npm run build`.

## Admin

- Nao usar `admin@example.com` / `password` em producao.
- Criar admin seguro com `php artisan admin:create`.
- Usar password forte e unica.
- Confirmar login em `/admin`.

## Scheduler e iCal

- Configurar cron:

```bash
* * * * * php /path/to/project/artisan schedule:run >> /dev/null 2>&1
```

- Confirmar que o scheduler lista `calendars:sync` hourly com `php artisan schedule:list`.
- Testar sincronizacao manual com `php artisan calendars:sync`.
- Adicionar e testar uma fonte iCal real no admin.
- Confirmar que uma falha iCal nao apaga bloqueios existentes.

## Paginas e SEO

- Testar `/`.
- Testar `/casas`.
- Testar pelo menos uma casa publica.
- Testar pelo menos uma unidade publica.
- Testar `/atividades`.
- Testar `/contactos`.
- Testar `/perguntas-frequentes`.
- Testar `/sitemap.xml`.
- Testar `/robots.txt`.
- Confirmar canonical URLs com dominio final.
- Confirmar `og:image` publico e acessivel.

## Formularios

- Testar pedido de reserva.
- Testar formulario de contacto.
- Confirmar que pedidos aparecem no admin.
- Confirmar que mensagens aparecem no admin.
- Confirmar que um pedido de reserva confirmado cria uma data bloqueada `Direct`.
- Em cancelamentos de pedidos confirmados, rever manualmente `Datas bloqueadas` antes de libertar o intervalo.

## Caches

- Correr `php artisan config:cache`.
- Correr `php artisan route:cache`.
- Correr `php artisan view:cache`.
- Se for preciso limpar caches, correr `php artisan optimize:clear`.

## Pos-deploy

- Abrir o site em janela anonima.
- Confirmar que assets CSS/JS carregam.
- Confirmar que `/admin` redireciona para login quando nao autenticado.
- Confirmar que o cron esta ativo nos logs do servidor.
- Confirmar que backups da base de dados estao configurados no alojamento.
