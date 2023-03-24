const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
var path = require('path');
const CopyPlugin = require('copy-webpack-plugin');

// change these variables to fit your project
const jsPath = './src/js';
const cssPath = './src/scss';
const outputPath = '../../wp-content/plugins/orc-options/dist/';
const localDomain = 'http://mysite.local';
const entryPoints = {
	// 'app' is the output name, people commonly use 'bundle'
	// you can have more than 1 entry point
	// 'script': jsPath + '/script.js',
	// 'app': jsPath + '/app.js',
	// 'style': cssPath + '/style.scss',
	'orc_options.min': cssPath + '/orc_options.scss',
	'orc.carousels.min': jsPath + '/orc.carousels.js',
	'orc.contacthandler.min': jsPath + '/orc.contacthandler.js',
	'orc.staff.min': jsPath + '/orc.staff.js',
	'orc.staff-admin.min': jsPath + '/orc.staff-admin.js',
	'orc.tour-admin.min': jsPath + '/orc.tour-admin.js',
	'orc.video-admin.min': jsPath + '/orc.video-admin.js',
	'orc_staff.min': cssPath + '/orc_staff.scss',
	'orc.videos.min': jsPath + '/orc.videos.js',
	'orc_videos.min': cssPath + '/orc_videos.scss',
};

module.exports = {
	entry: entryPoints,
	output: {
		path: path.resolve(__dirname, outputPath),
		filename: 'js/[name].js',
	},
	plugins: [
		new MiniCssExtractPlugin({
			filename: 'css/[name].css',
		}),

		new CopyPlugin({
			patterns: [
				{
					from: "./src/index.php",
					to: "../../../plugins/orc-options/dist/",
				},
				{
					from: "./src/index.php",
					to: "../../../plugins/orc-options/dist/css/",
				},
				{
					from: "./src/index.php",
					to: "../../../plugins/orc-options/dist/js/",
				},
				{
					from: "./includes",
					to: "../../../plugins/orc-options/includes",
				},
				{
					from: "./templates",
					to: "../../../plugins/orc-options/templates",
				},
				{
					from: "./vendor",
					to: "../../../plugins/orc-options/vendor",
				},
				{
					from: "./README.md",
					to: "../../../plugins/orc-options/",
				},
				{
					from: "./orc_options.php",
					to: "../../../plugins/orc-options/",
				},
				{
					from: "./LICENSE",
					to: "../../../plugins/orc-options/",
				},
				{
					from: "./index.php",
					to: "../../../plugins/orc-options/",
				},
				{
					from: "./composer.json",
					to: "../../../plugins/orc-options/",
				},
			]
		}),

		// Uncomment this if you want to use CSS Live reload
		/*
		new BrowserSyncPlugin({
		  proxy: localDomain,
		  files: [ outputPath + '/*.css' ],
		  injectCss: true,
		}, { reload: false, }),
		*/
	],
	module: {
		rules: [
			{
				test: /\.s?[c]ss$/i,
				use: [
					MiniCssExtractPlugin.loader,
					'css-loader',
					'sass-loader'
				]
			},
			{
				test: /\.sass$/i,
				use: [
					MiniCssExtractPlugin.loader,
					'css-loader',
					{
						loader: 'sass-loader',
						options: {
							sassOptions: { indentedSyntax: true }
						}
					}
				]
			},
			{
				test: /\.(jpg|jpeg|png|gif|woff|woff2|eot|ttf|svg)$/i,
				use: 'url-loader?limit=1024'
			}
		]
	},
};
