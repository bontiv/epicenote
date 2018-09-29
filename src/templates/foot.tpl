</div>

<!-- Begin page content -->

<footer class="footer">
    <div class="container">
        <p class="text-muted">&COPY; EPITANIME 2012 - 2018 - <a style="color:#777" href="{mkurl action="rgpd"}">Mentions légales et RGPD</a></p>
    </div>
</footer>


{* Pour le tracking visiteurs *}
<!-- Piwik -->
<script type="text/javascript">
    var _paq = _paq || [];
    _paq.push(['trackPageView']);
    _paq.push(['enableLinkTracking']);
    {if $_user}
    _paq.push(['setUserId', '{$_user.user_name|escape}']);
    {else}
    _paq.push(['resetUserId']);
    {/if}
    // require user consent before processing data
    _paq.push(['trackPageview']);

    (function () {
        var u = "//stats.bonnetlive.net/";
        _paq.push(['setTrackerUrl', u + 'piwik.php']);
        _paq.push(['setSiteId', 10]);
        var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
        g.type = 'text/javascript';
        g.async = true;
        g.defer = true;
        g.src = u + 'piwik.js';
        s.parentNode.insertBefore(g, s);
    })();
</script>
<noscript><p><img src="//stats.bonnetlive.net/piwik.php?idsite=10" style="border:0;" alt="" /></p></noscript>
<!-- End Piwik Code -->


</body>

</html>
