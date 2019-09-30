<div class="hide">
    <a data-fancybox data-src="#cert-<?= CATALOG_CUSTOM_CERT_ENUM ?>" href="javascript:;"></a>
    <div id="cert-<?= CATALOG_CUSTOM_CERT_ENUM ?>">
        <form action="" style="max-width: 400px; margin:0 auto;">
            <label for=""> На какую сумму будет сертификат ?
                <input type="number" id="cert_summ">
            </label>
            <a class="btn_ico" onclick="jQuery.fancybox.close();"> Принять!</a>
        </form>
    </div>
</div>
<script>
    let customCert = document.querySelector('[data-onevalue="<?=CATALOG_CUSTOM_CERT_ENUM?>"]');
    if (customCert) {
        customCert.addEventListener('click', function (event) {

            document.querySelector('[data-src="#cert-<?=CATALOG_CUSTOM_CERT_ENUM?>"]').dispatchEvent(new MouseEvent('click', {bubbles: true}));
        })
    }
</script>