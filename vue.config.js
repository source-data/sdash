module.exports = {
	// configureWebpack: {
	//   devServer: {
	//     host: 'localhost'
	//   }
	// },

	publicPath: process.env.NODE_ENV === 'production'
		? '/'
		: '/',
	chainWebpack: config => {
		config.module
			.rule('i18n')
			.resourceQuery(/blockType=i18n/)
			.type('javascript/auto')
			.use('i18n')
			.loader('@kazupon/vue-i18n-loader')
	},
	runtimeCompiler: true,
	transpileDependencies: [
		/\bvue-awesome\b/
	]
}
