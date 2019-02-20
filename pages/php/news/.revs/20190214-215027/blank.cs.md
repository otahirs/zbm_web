---
title: news
date: '2018-10-25'
---

{% if (authorize(['site.novinky'])) %}
    {{ phpNews( grav.user.username) }}
{% endif %}