<template>
    <div style="padding: 0 70px 0 70px">
        <input type="text" class="form-control" placeholder="Link to a file to be attached: http://s3.aws.com/bucket/891n3ksde/901234hkls.zip">
        <button class="btn btn-light btn-sm mt-2">Download and attach to panel</button>
    </div>
</template>

<script>
export default {
    props: ["panel"],
}
</script>

<style scoped>
</style>
