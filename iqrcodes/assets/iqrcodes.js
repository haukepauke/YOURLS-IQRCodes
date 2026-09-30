function iqrcodes(url) {
	var shorturl = url || $('#copylink').val();
	if (!shorturl) {
		return;
	}

	var qrurl = shorturl.replace(/\/$/, '') + '.qr';
	var formats = [
		{ value: 'png', label: 'PNG' },
		{ value: 'jpeg', label: 'JPEG' },
		{ value: 'svg', label: 'SVG' }
	];
	var selectedFormat = typeof iqrcodes_imagetype === 'string' ? iqrcodes_imagetype : 'png';
	if (selectedFormat === 'jpg') {
		selectedFormat = 'jpeg';
	}
	if (selectedFormat !== 'png' && selectedFormat !== 'jpeg' && selectedFormat !== 'svg') {
		selectedFormat = 'png';
	}
	var formatUrl = function (format, download) {
		return qrurl + '?format=' + encodeURIComponent(format) + (download ? '&download=1' : '');
	};
	$('#qrcode').remove();
	var formatSelect = $('<select>', { id: 'iqrcodes-format', 'aria-label': 'QR code format' });
	for (var i = 0; i < formats.length; i++) {
		var format = formats[i];
		var option = $('<option>', { value: format.value }).text(format.label);
		if (format.value === selectedFormat) {
			option.prop('selected', true);
		}
		formatSelect.append(option);
	}
	var image = $('<img>', { id: 'iqrcodes-image', src: formatUrl(selectedFormat), alt: 'QR Code' }).css({ width: '100px', height: '100px' });
	var download = $('<a>', { id: 'iqrcodes-download', text: 'Download ' + selectedFormat.toUpperCase() }).attr({ href: formatUrl(selectedFormat, true), download: '' });
	formatSelect.on('change', function () {
		var format = $(this).val();
		image.attr('src', formatUrl(format));
		download.attr('href', formatUrl(format, true)).text('Download ' + format.toUpperCase());
	});
	$('#shareboxes').append(
		$('<div>', { id: 'qrcode', 'class': 'share' })
			.append($('<h3>').text('QR Code'))
			.append(image)
			.append($('<div>', { 'class': 'iqrcodes-download' })
				.append($('<label>', { 'for': 'iqrcodes-format' }).text('Format: '))
				.append(formatSelect)
				.append($('<br>'))
				.append(download))
	);
}

$(document).ready(function () {
	$('.button_share').click(function () { iqrcodes(); });
	$('a[href="#stat_tab_share"]').click(function () { iqrcodes(); });
	iqrcodes();
});
