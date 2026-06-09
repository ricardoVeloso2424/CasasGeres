# Casas Geres

Aplicacao Laravel para gerir casas, unidades alugaveis, pedidos de reserva, mensagens de contacto, comodidades, atividades e disponibilidade por iCal.

## Requisitos

- PHP 8.2+
- Composer
- Node.js e NPM
- MySQL
- Cron no servidor para o scheduler Laravel
- Extensoes PHP comuns em Laravel: `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `hash`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `session`, `tokenizer`, `xml`
- `ext-intl` recomendada para producao, embora a versao atual do parser iCal funcione sem ela

## Instalacao para producao

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Antes de correr os comandos finais, editar `.env` com os valores reais de producao:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://dominio-final`
- `APP_TIMEZONE=Europe/Lisbon`
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `SITE_NAME`, `SITE_PHONE`, `SITE_WHATSAPP`, `SITE_EMAIL`, `SITE_LOCATION`
- `SITE_DEFAULT_DESCRIPTION`, `SITE_DEFAULT_OG_IMAGE`
- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`

Depois de configurar a base de dados, criar o utilizador administrador:

```bash
php artisan admin:create
```

O seeder cria `admin@example.com` com password `password` apenas em `local` e `testing`. Em `production`, esse admin demo nao e criado automaticamente.

> Em producao usar `php artisan migrate --force` (sem `--seed`). Os seeders incluem conteudo de demonstracao (casas, atividades e pedidos de reserva ficticios) e nao devem correr na base de dados de producao. O conteudo real e criado pelo admin.

## Desenvolvimento local

```bash
composer install
npm install
php artisan migrate:fresh --seed
npm run build
php artisan test
php artisan serve
```

## Calendarios iCal

As fontes iCal sao geridas no admin em `/admin/calendar-sources`. Cada fonte pertence a uma unidade alugavel e tem:

- unidade
- plataforma, por exemplo Booking, Airbnb, Vrbo ou Outro
- URL iCal
- estado ativo/inativo

Para adicionar uma fonte Booking ou Airbnb:

1. Entrar no admin.
2. Abrir `Calendarios iCal`.
3. Criar uma nova fonte.
4. Escolher a unidade.
5. Preencher a plataforma.
6. Colar a URL iCal fornecida pela plataforma.
7. Guardar.
8. Clicar em `Sincronizar`.

A sincronizacao cria ou atualiza `blocked_dates` associados a essa fonte. A disponibilidade publica e indicativa e pode ter atraso entre a alteracao na plataforma externa e a proxima sincronizacao.

## Sincronizacao manual

Sincronizar todas as fontes ativas:

```bash
php artisan calendars:sync
```

Sincronizar uma fonte especifica:

```bash
php artisan calendars:sync --source=ID
```

O botao `Sincronizar` na listagem admin faz o mesmo para uma unica fonte.

## Reservas diretas

Pedidos feitos pelo formulario publico entram como `pending`.

Quando um pedido e confirmado no admin, a aplicacao valida novamente a disponibilidade da unidade e cria uma entrada em `blocked_dates` com:

- `source=Direct`
- `external_uid=direct-booking-request-{id}`
- inicio igual ao check-in
- fim exclusivo igual ao check-out

Confirmar o mesmo pedido novamente nao duplica a data bloqueada. Se o status for alterado de `confirmed` para `cancelled`, a data bloqueada nao e apagada automaticamente nesta fase; rever e tratar manualmente em `Datas bloqueadas` quando for seguro libertar o intervalo.

## Scheduler

O scheduler Laravel esta configurado em `bootstrap/app.php` para correr a sincronizacao iCal de hora a hora:

```php
Schedule::command('calendars:sync')->hourly();
```

Em producao, configurar o cron do servidor:

```bash
* * * * * php /path/to/project/artisan schedule:run >> /dev/null 2>&1
```

Para ver a agenda:

```bash
php artisan schedule:list
```

## Falhas de sincronizacao

Uma falha de sincronizacao nao apaga bloqueios existentes.

Comportamento atual:

- URL 404 ou outro erro HTTP: marca a fonte como `failed`, guarda erro curto e nao altera `last_synced_at`.
- Resposta vazia: marca como `failed` e nao remove bloqueios.
- Conteudo que nao parece iCal: marca como `failed` e nao remove bloqueios.
- Calendario iCal valido mas sem eventos validos: marca como `failed` e nao remove bloqueios por seguranca.
- Sucesso: marca como `success`, limpa `last_sync_error`, atualiza `last_synced_at` e remove apenas bloqueios antigos da mesma `calendar_source`.

As URLs completas nao sao registadas nos logs para evitar expor tokens privados.

## SEO e ficheiros publicos

O site gera:

- metatags basicas por pagina
- JSON-LD simples na homepage e pagina de unidade
- `/sitemap.xml`
- `/robots.txt`

`APP_URL` deve apontar para o dominio final antes de correr caches, para sitemap, robots e canonical ficarem corretos.

## Testes e validacao

```bash
php artisan migrate:fresh --seed
npm run build
php artisan test
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize:clear
```

Antes do deploy, consultar tambem [docs/deploy-checklist.md](docs/deploy-checklist.md).
