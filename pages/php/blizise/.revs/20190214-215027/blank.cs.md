---
title: blizise
date: '2018-10-25'
---

{% if (authorize(['site.blizise'])) %}
      {{ phpEditBliziSe( grav.user.username) }} {# vypise php kod pro zpracovani formulare #}
{% endif %}