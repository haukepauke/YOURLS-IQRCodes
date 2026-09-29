function iqrcodes(url) {
	var shorturl = url || $('#copylink').val();
	if (!shorturl) {
		return;
	}

	var qrcimg = shorturl.replace(/\/$/, '') + '.qr';
	$('#qrcode').remove();
	$('#shareboxes').append(
		$('<div>', { id: 'qrcode', 'class': 'share' })
			.append($('<h3>').text('QR Code'))
			.append($('<img>', { id: 'iqrcodes-image', src: qrcimg, alt: 'QR Code' }).css({ width: '100px', height: '100px' }))
	);
}

$(document).ready(function () {
	$('.button_share').click(function () { iqrcodes(); });
	$('a[href="#stat_tab_share"]').click(function () { iqrcodes(); });
	iqrcodes();
});
