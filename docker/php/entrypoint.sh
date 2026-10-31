#!/bin/sh
set -e
cd /var/www/html

# Install/upgrade Grav core into the shared volume (nginx serves static files from it).
want=$(cat /usr/src/grav-core/.grav-version)
have=$(cat .grav-version 2>/dev/null || true)
if [ "${ZBM_SYNC_CORE:-1}" = 1 ] && [ "$want" != "$have" ]; then
    echo "zbm: installing Grav core $want (was: ${have:-none})"
    rsync -a --delete --exclude /user /usr/src/grav-core/ /var/www/html/
fi

mkdir -p cache logs tmp backup images assets user/data user/accounts
chown -R www-data:www-data cache logs tmp backup images assets user/data user/accounts

# Local login account (user/accounts is gitignored, never deployed).
if [ "${ZBM_CREATE_TEST_USER:-1}" = 1 ] && [ ! -f user/accounts/test.yaml ]; then
    su -s /bin/sh www-data -c "php bin/plugin login new-user -u test -p Test1234 \
        -e test@example.com -P b --admin-type admin -N 'Test User' -l cs" \
        || echo "zbm: WARNING: creating test user failed"
fi

# Local mail config (gitignored): Grav -> sendmail -bs -> mailpit, nothing leaves the machine.
if [ ! -f user/config/plugins/email.yaml ]; then
    cat > user/config/plugins/email.yaml <<'YAML'
enabled: true
from: web@zabiny.club
from_name: 'ZBM web (local)'
to: dev@example.com
mailer:
  engine: sendmail
  sendmail:
    bin: '/usr/sbin/sendmail -bs'
YAML
    chown www-data:www-data user/config/plugins/email.yaml
fi

exec docker-php-entrypoint "$@"
