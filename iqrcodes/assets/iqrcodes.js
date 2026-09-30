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
	var selectedFormat = iqrcodes_imagetype === 'jpg' ? 'jpeg' : iqrcodes_imagetype;
	if (!formats.some(function (format) { return format.value === selectedFormat; })) {
		selectedFormat = 'png';
	}
	var formatUrl = function (format, download) {
		return qrurl + '?format=' + encodeURIComponent(format) + (download ? '&download=1' : '');
	};
	$('#qrcode').remove();
	var formatSelect = $('<select>', { id: 'iqrcodes-format', 'aria-label': 'QR code format' });
	formats.forEach(function (format) {
		formatSelect.append($('<option>', { value: format.value, text: format.label, selected: format.value === selectedFormat }));
	});
	var image = $('<img>', { id: 'iqrcodes-image', src: formatUrl(selectedFormat), alt: 'QR Code' }).css({ width: '100px', height: '100px' });
	var download = $('<a>', { id: 'iqrcodes-download', text: 'Download ' + selectedFormat.toUpperCase(), download: '' }).attr('href', formatUrl(selectedFormat, true));
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
				.append(formatSelect)
				.append(download))
	);
}

$(document).ready(function () {
	$('.button_share').click(function () { iqrcodes(); });
	$('a[href="#stat_tab_share"]').click(function () { iqrcodes(); });
	iqrcodes();
});
