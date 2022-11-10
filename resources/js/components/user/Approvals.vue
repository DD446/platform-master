<template>
    <div>
        <add-approval
            :can-use-auphonic="isAuphonicAllowed"></add-approval>

        <div>
            <div class="row mt-4">
                <h3>{{ $t('approvals.header_approved_services') }}</h3>
            </div>
            <div class="row mt-4">
                <approved-service
                    v-for="(approval) in approvals"
                    :key="approval.id"
                    :approval="approval"></approved-service>

                <b-card v-show="approvals.length < 1">
                    <b-card-text>
                        {{ $t('approvals.hint_no_approvals') }}
                    </b-card-text>
                </b-card>
            </div>
        </div>
    </div>
</template>

<script>
import ApprovedService from "./ApprovedService";
import AddApproval from "./AddApproval";

export default {
    name: "Approvals",

    components: {AddApproval, ApprovedService},

    props: {
        approvals: Array,

        canUseAuphonic: {
            type: Boolean,
            default: false
        }
    },

    data() {
        return {
            allowAuphonic: false,
        }
    },

    computed: {
        isAuphonicAllowed() {
            return this.canUseAuphonic
                && !this.approvals.some(function(o){return o["service"] === "auphonic";});
        }
    },

    mounted() {
    }
}
</script>

<style scoped>

</style>
