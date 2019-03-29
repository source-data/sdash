<template>
	<div class = 'userIcon'>
		<svg class='user_icon'>
			<g>
				<circle :style='"fill:"+colour' cx="20" cy="20" r="20"></circle>
				<text x="50%" y="50%" text-anchor="middle" :stroke="stroke" stroke-width="1.5px" dy=".3em">{{name|initials}}</text>
			</g>
		</svg>
	</div>
</template>

<script>
export default {
	name: 'userIcon',
	props: {
		name: {
			type: String,
			required: true
		}
	},
	computed: {
		colour () {
			let str = this.name
			let hash = 0;
			for (let i = 0; i < str.length; i++) {
				hash = str.charCodeAt(i) + ((hash << 5) - hash);
			}
			let colour = '#';
			for (var i = 0; i < 3; i++) {
				let value = (hash >> (i * 8)) & 0xFF;
				colour += ('00' + value.toString(16)).substr(-2);
			}
			return colour;			
		},
		stroke () {
			// Variables for red, green, blue values
			let r, g, b, hsp;
			// If RGB --> Convert it to HEX: http://gist.github.com/983661
			let color = +("0x" + this.colour.slice(1).replace( this.colour.length < 5 && /./g, '$&$&'));
			r = color >> 16;
			g = color >> 8 & 255;
			b = color & 255;
			// HSP (Highly Sensitive Poo) equation from http://alienryderflex.com/hsp.html
			hsp = Math.sqrt(
				0.299 * (r * r) +
				0.587 * (g * g) +
				0.114 * (b * b)
			);
			// Using the HSP value, determine whether the color is light or dark
			if (hsp>127.5) {
				return "#000";
			} 
			else {
				return "#FFF";
			}
		}
	}
}
</script>

<style scoped>

svg.user_icon {
	width: 40px;
	height: 40px;
}


</style>