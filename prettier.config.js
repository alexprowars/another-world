/** @type {import('prettier').Config} */
export default {
	useTabs: true,
	tabWidth: 4,
	printWidth: 160,
	singleQuote: true,
	singleAttributePerLine: false,
	bracketSpacing: true,
	bracketSameLine: false,
	semi: true,
	trailingComma: 'all',
	arrowParens: 'avoid',
	htmlWhitespaceSensitivity: 'ignore',
	vueIndentScriptAndStyle: true,
	endOfLine: 'lf',
	proseWrap: 'preserve',
	overrides: [
		{
			files: ['*.json', '*.jsonc'],
			options: {
				useTabs: false,
			},
		},
	],
};
