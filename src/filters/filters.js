import Vue from 'vue'
import moment from 'moment'

Vue.filter('formatDate', value => {
	if (value) {
		return moment(String(value)).isValid() ? moment(String(value)).format('MMM Do YYYY') : 'Invalid Date'
	}
	return value
})

Vue.filter('formatDateTime', value => {
	if (value) {
		return moment(String(value)).isValid() ? moment(String(value)).format('MMM Do YYYY HH:mm') : 'Invalid Date'
	}
	return value
})

Vue.filter('formatTime', value => {
	if (value) {
		return moment(String(value)).isValid() ? moment(String(value)).format('HH:mm') : 'Invalid Date'
	}
	return value
})

Vue.filter('nl2br', value => {
	return value.replace('\n', '<br>')
})

Vue.filter('formatModality', value => {
	return value.split(',').join(' / ')
})

Vue.filter('formatPermissions', item => {
	let perms = []
	_.forEach(item, (value, key) => {
		if (key.indexOf('permission') > -1 && value) {
			perms.push(key.replace('_permission', ''))
		}
	})
	return (perms.length) ? perms.join(', ') : '-'
})

Vue.filter('initials', item => {
	var initials = item.match(/\b\w/g) || [];
	return ((initials.shift() || '') + (initials.pop() || '')).toUpperCase();
})