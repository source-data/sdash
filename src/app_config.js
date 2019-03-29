export const serverURL = process.env.VUE_APP_URL_API

export const siteTitle = 'SDash'

export const loginType = 'local'

export const getHeader = function () {
	const headers = {
		'Accept': 'application/json'
	}
	return headers
}
