---
title: news
date: '2018-10-25'
access:
    site:
        novinky: true
---

{% if (authorize(['site.novinky'])) %}
    {{ phpNews( grav.user.username) }}
{% endif %}