import axios from 'axios'
import { serverURL } from '@/app_config'

export var HTTP = axios.create({ baseURL: serverURL })

HTTP.defaults.headers.common['Accept'] = 'application/json';
HTTP.defaults.headers.common['authorization'] = ''
HTTP.interceptors.response.use(function (response) {
	return response
}, function(error){
	return Promise.reject(error.response.data);

	// if (error.response.status == 501) return Promise.reject(error.response.data);
	// else return Promise.reject(null);
})