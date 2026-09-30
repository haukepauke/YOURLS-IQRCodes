function iqrcodes(url) {
	var shorturl = url || $('#copylink').val();
	if (!shorturl) {
		return;
	}
	if ($('#qrcode').length) {
		return;
	}

	var qrurl = shorturl.replace(/\/$/, '') + '.qr';
	var formatUrl = function (format, download) {
		return qrurl + '?format=' + encodeURIComponent(format) + (download ? '&download=1' : '');
	};
	$('#qrcode').remove();
	var image = $('<img>', { id: 'iqrcodes-image', src: formatUrl('png'), alt: 'QR Code' }).css({ width: '100px', height: '100px' });
	var downloads = $('<p>', { 'class': 'iqrcodes-download' }).append('Download: ');
	var formats = [
		{ value: 'png', label: 'PNG' },
		{ value: 'jpeg', label: 'JPEG' },
		{ value: 'svg', label: 'SVG' }
	];
	for (var i = 0; i < formats.length; i++) {
		var format = formats[i];
		downloads.append($('<a>', { text: format.label }).attr({ href: formatUrl(format.value, true), download: '' }));
	}
	$('#shareboxes').append(
		$('<div>', { id: 'qrcode', 'class': 'share' })
			.append($('<h3>').text('QR Code'))
			.append(image)
			.append(downloads)
	);
}

$(document).ready(function () {
	$('.button_share').click(function () { iqrcodes(); });
	$('a[href="#stat_tab_share"]').click(function () { iqrcodes(); });
	iqrcodes();
});
