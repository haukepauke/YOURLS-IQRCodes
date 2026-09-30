function iqrcodes(shorturl) {
	shorturl = shorturl || $('#copylink').val();
	if (!shorturl) {
		return;
	}

	var qrurl = shorturl.replace(/\/$/, '') + '.qr';
	var panel = $('#qrcode');
	if (!panel.length) {
		panel = $('<div>', { id: 'qrcode', 'class': 'iqrcodes-panel' })
			.append($('<h3>').text('QR Code'))
			.append($('<img>', { id: 'iqrcodes-image', alt: 'QR Code', width: 100, height: 100 }))
			.append($('<p>', { 'class': 'iqrcodes-download' }).text('Download: '));
		$('#sharebox').append(panel);
	}

	$('#iqrcodes-image').attr('src', qrurl + '?format=png');
	var downloads = panel.find('.iqrcodes-download').empty().text('Download: ');
	var formats = ['png', 'jpeg', 'svg'];
	for (var i = 0; i < formats.length; i++) {
		var format = formats[i];
		downloads.append($('<a>', { text: format.toUpperCase() }).attr({
			href: qrurl + '?format=' + format + '&download=1',
			download: '',
			target: '_blank',
			rel: 'noopener noreferrer'
		}));
	}
}

$(document).ready(function () {
	$(document).on('click', 'a[id^="share-button-"]', function () { iqrcodes(); });
	$('a[href="#stat_tab_share"]').on('click', function () { iqrcodes(); });
	$(document).ajaxSuccess(function (event, xhr, settings, data) {
		if (data && data.status === 'success' && data.shorturl) {
			iqrcodes(data.shorturl);
		}
	});
	iqrcodes();
});
