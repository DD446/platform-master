<template>
    <div class="row justify-content-center">
        <div class="col-12 col-md-6 col-lg-5">
            <div class="card card-lg text-center">
                <div class="card-body">
                    <i class="icon-add-to-list display-4 opacity-20"></i>
                    <h1 class="h5">{{ $t('feeds.header_empty_list') }}</h1>
                    <p>
                        {{ $t('feeds.text_empty_list') }}
                    </p>
                    <a href="/podcast/wizard" class="btn btn-lg btn-primary"
                       v-b-popover.hover.auto="$t('feeds.popover_create_channel')">{{ $t('feeds.button_empty_list') }}</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "empty-channel-list"
    }
</script>

<style scoped>

</style>
