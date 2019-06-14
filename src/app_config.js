module.exports = {
	serverURL:  process.env.NODE_ENV === 'production'
		? '/api/index.php'
		: 'http://sdash/api/index.php',
	publicPath: process.env.NODE_ENV === 'production'
		? '/'
		: '/',
	siteTitle: process.env.VUE_APP_TITLE,
	loginType: 'local',
	getHeader () {
		const headers = {
			'Accept': 'application/json'
		
		}
		return headers
	}
}