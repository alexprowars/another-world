export function time (value, separator, full)
{
	if (typeof separator === 'undefined')
		separator = '';

	if (typeof full === 'undefined')
		full = false;

	if (value < 0)
		return '-';

	let dd = Math.floor(value / (24 * 3600));
	let hh = Math.floor(value / 3600 % 24);
	let mm = Math.floor(value / 60 % 60);
	let ss = Math.floor(value / 1 % 60);

	let time = '';

	if (dd !== 0)
		time += ((separator !== '' && dd < 10) ? '0' : '')+dd+((separator !== '') ? separator : ' д. ');

	if (hh > 0 || full)
		time += ((separator !== '' && hh < 10) ? '0' : '')+hh+((separator !== '') ? separator : ' ч. ');

	if (mm > 0 || full)
		time += ((separator !== '' && mm < 10) ? '0' : '')+mm+((separator !== '') ? separator : ' м. ');

	time += ((separator !== '' && ss < 10) ? '0' : '')+ss+((separator !== '') ? '' : ' с. ');

	if (!time.length)
		time = '-';

	return time;
}