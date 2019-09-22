<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;

class piwikPlugin extends Plugin
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'onAssetsInitialized' => ['onAssetsInitialized', 0]
        ];
    }

    /**
     * Add piwik JS
     */
    public function onAssetsInitialized()
    {
        if ($this->isAdmin()) {
            return;
        }

        $siteId = trim($this->config->get('plugins.piwik.siteId'));
        $sitePiWikURL = trim($this->config->get('plugins.piwik.sitePiWikURL'));
        

        $search = array('http://','https://');
        $sitePiWikURL = str_replace($search,'',$sitePiWikURL);
        if ($siteId && $sitePiWikURL) {
            $init = "
<!-- Matomo -->
<script type=\"text/javascript\">
  var _paq = window._paq || [];
  /* tracker methods like \"setCustomDimension\" should be called before \"trackPageView\" */
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u=\"//{$sitePiWikURL}/\";
    _paq.push(['setTrackerUrl', u+'matomo.php']);
    _paq.push(['setSiteId', '{$siteId}']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<!-- End Matomo Code -->
            ";
            $this->grav['assets']->addInlineJs($init);
        }
    }
}
?>
