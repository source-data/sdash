<template>
	<div>
		<table>
			<tr v-for="attachment in attachments" :key="attachment.id">
				<td style="padding-right: 10px">
					<a :href="downloadUrl(attachment)">
						<v-icon name="file-download"></v-icon>
						{{attachment.filename}}
					</a>
				</td>
				<td>
					{{ attachment.description }}
				</td>
			</tr>
		</table>
		<AttachmentUpload :panel="panel" />
	</div>
</template>

<script>

import AttachmentUpload from './upload'

export default {
	name: 'DataAttachments',
	props: ["panel"],
	components: {
		AttachmentUpload
	},
	computed: {
		attachments () {
			return [
				{ id: 1, panel_id: 'xxx', filename: 'data.xlsx', description: 'raw data', created_at: Date.now() },
				{ id: 2, panel_id: 'xxx', filename: 'processed.csv', description: 'pre processed data with the python script', created_at: Date.now() },
				{ id: 3, panel_id: 'xxx', filename: 'process.py', description: 'python script to process data', created_at: Date.now() },
			]
		}
	},

	methods: {
		downloadUrl (attachment) {
			return `/panels/attachments/${attachment.id}`
		}
	}
}
</script>

<style>

</style>