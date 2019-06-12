module.exports = {
	serverURL: process.env.VUE_APP_URL_API,
	publicPath: process.env.NODE_ENV === 'production'
		? '/sdash/'
		: '/',
	siteTitle: 'SDash',
	loginType: 'local',
	getHeader () {
		const headers = {
			'Accept': 'application/json'
		
		}
		return headers
	}
}