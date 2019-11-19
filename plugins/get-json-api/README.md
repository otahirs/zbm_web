# Get Json Api Plugin

## usage
```
{% try %}
   {% set notify = "https://getgrav.org/notifications.json"|getJson %}
{% catch %}
   <div class="notices red">
        <p> Error getting API </p>
    </div>
{% endcatch %}

use notify array object
```