const pipe = (...fns) => x => fns.reduce((y, f) => f(y), x);  // Function chaining
const capitalize = (s) => typeof s !== 'string' ? '' : s.charAt(0).toUpperCase() + s.slice(1);

export class LegendTagger {

	constructor(legend) {
		this.legendText = legend;
	}

	fetchTaggedXML(callback = (x) => console.log(x)) {
		const https = require('https');
		const querystring = require('querystring');

		let result = {};

		const postData = querystring.stringify({
			format: 'xml',
			text: this.legendText
		});

		let options = {
			hostname: 'smtag.sourcedata.io',
			path: '/smtag',
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
				'Content-Length': postData.length
			}
		};

		let req = https.request(options, (res) => {
			if (res.statusCode != 200) {
				console.log('Smtag request failed with status ', res.statusCode);
				console.log('headers:', res.headers);
			}
			res.on('data', (d) => {
				this.taggedLegendXML = d;
				const transformer = new taggedLegendXML2labels(d);
				result = callback(transformer.labels);
			});
		});

		req.on('error', (e) => {
			console.error(e);
		});

		req.write(postData);
		req.end();

		return result;
	}

}

class taggedLegendXML2labels {

	constructor(legendXML) {
		this.tagName = "sd-tag";
		this.keyAttribute = 'role'; // key is the class of labels
		this.labelAttribute = '_';//'type';
		this.legendXML = legendXML;
		this.selectTagWithLabel = (tagArray, labelAttribute) => tagArray.filter(tag => tag.hasOwnProperty(labelAttribute));
		this.removeDuplicateLabel = labels => {
			// keep unique labels for each category
			const distinct = (value, index, self) => self.indexOf(value) === index;
			for (let key in labels) {
				labels[key] = labels[key].filter(distinct);
			}
			return labels;
		};
		this.aggregateLabelByKey = labels => labels.reduce((labelFullSet, labelSet) => {
			for (let [key, labels] of Object.entries(labelSet)) {
				if (labelFullSet.hasOwnProperty(key))
					labelFullSet[key].push(...labels);
				else
					labelFullSet[key] = labels;
			}
			return labelFullSet;
		}, {});
	}
	get labels() {
		const selectTagWithKey = tagArray => tagArray.filter(tag => tag.hasOwnProperty(this.keyAttribute));
		const selectTagWithLabel = tagArray => this.selectTagWithLabel(tagArray, this.labelAttribute);
		const tags2labels = tagArray => this.tags2labels(tagArray, this.labelAttribute, this.keyAttribute);
		const categoryLabels = this.getLabelByDefinedKey('Method', 'category');
		const mergeCategoryInfo = labelsObj => this.aggregateLabelByKey([labelsObj, categoryLabels]);
		const process = pipe(selectTagWithKey, selectTagWithLabel, tags2labels, this.aggregateLabelByKey, mergeCategoryInfo, this.removeDuplicateLabel);
		return process(this.sdTags);
	}
	getLabelByDefinedKey(keyName, labelAttribute) {
		labelAttribute = labelAttribute || this.labelAttribute;
		const tags2labels = tagArray => this.tags2labels(tagArray, labelAttribute, keyName, true);
		const process = pipe(tagArray => this.selectTagWithLabel(tagArray, labelAttribute), tags2labels, this.aggregateLabelByKey, this.removeDuplicateLabel);
		return process(this.sdTags);
	}
	get legend() {
		const xml2js = require('xml2js').parseString;
		const root_xml = xml => '<root>' + xml + '</root>';
		let res
		xml2js(root_xml(this.legendXML), function (err, result) {
			res = result;
		});
		return res//JSON.parse(xml2json.toJson(root_xml(this.legendXML), { reversible: true }));
	}
	get sdTags() {
		let tags = this.getSdTags(this.legend);
		return tags.map(tag => Object.assign(tag['$'], {'_': tag['_']}));
	}
	getSdTags(legendObj) {
		let results = [];
		for (let i in legendObj) {
			if (i == this.tagName) {
				if (Array.isArray(legendObj[i]))
					results.push(...legendObj[i]);
				else
					results.push(legendObj[i]);
			} else if (typeof legendObj[i] == 'object') {
				results.push(...this.getSdTags(legendObj[i]));
			}
		}
		return results;
	}
	tags2labels(tagArray, labelAttribute, keyAttributeOrDefinedKey, isKeyDefined) { // isKeyDefined is optional, default false 
		return tagArray.map(tag => {
			let label = {}, key = isKeyDefined ? keyAttributeOrDefinedKey : capitalize(tag[keyAttributeOrDefinedKey]);
			label[key] = [tag[labelAttribute]];
			return label;
		});
	}
}
