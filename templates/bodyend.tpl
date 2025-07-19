<script type="text/javascript">
{include file="jsDropDown.tpl"}
{include file="jsRequiredFields.tpl"}
{include file="jsRestApi.tpl"}
</script>

<script type="text/javascript">
let qrCodeSettings = {
    width: 160,
	height: 160,
	colorDark : "#000000",
	colorLight : "#ffffff",
	correctLevel : QRCode.CorrectLevel.H,
    useSVG: false,
    // Custom options
    altText: 'QR-Code',
    titleText: 'QR-Code',
    removeCanvas: true
}

let qrCodeFields = document.querySelectorAll('div.qrCodeField')

for (let qrField of qrCodeFields) {
    qrCodeSettings['text'] = qrField.dataset.qrvalue
    qrCodeSettings['altText'] = qrCodeSettings['text']
    qrCodeSettings['titleText'] = qrCodeSettings['text']
    qrCodeSettings['removeCanvas'] = true
    new QRCode(qrField, qrCodeSettings)
}
</script>