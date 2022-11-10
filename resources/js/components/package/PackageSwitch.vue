<template>
    <div>
        <b-overlay :show="isLoading" rounded="sm">
            <b-form action="packageChangeRoute" method="post" @submit.prevent="onSubmit" v-show="this.packageId !== this.packageIdSelected">
                <input type="hidden" name="id" :value="packageId">
                    <b-button type="submit" :variant="buttonVariant()" v-b-popover.hover.top="buttonPopover()">{{ buttonText() }}</b-button>
            </b-form>
        </b-overlay>

        <b-button
            type="submit"
            :variant="buttonVariant()"
            v-b-popover.hover.top="buttonPopover()"
            v-show="this.packageId === this.packageIdSelected"
        >{{ buttonText() }}</b-button>

<!--        <b-button
            :href="'/paket/' + this.packageId + '/' + this.packageName + '/buchen'"
            :variant="buttonVariant()"
            v-b-popover.hover.top="buttonPopover()"
            v-show="this.packageId !== this.packageIdSelected">{{ buttonText() }}</b-button>-->
    </div>
</template>

<script>
    import {eventHub} from '../../app';

    export default {
        name: "PackageSwitch",

        props: {
            packageId: String,
            packageIdSelected: String,
            packageName: String,
            packageChangeRoute: String,
        },

        data() {
            return {
                selectedPackageId: this.packageIdSelected,
                isLoading: false,
            }
        },

        computed: {
            isActivePackage() {
                return this.packageId === this.selectedPackageId;
            },
        },

        methods: {
            buttonPopover() {
                if (this.isActivePackage) {
                    return this.$t('package.popover_package_active');
                }
                return this.$t('package.popover_package_change', {'name': this.packageName});
            },
            buttonText() {
                if (this.isActivePackage) {
                    return this.$t('package.text_selected_package');
                }
                return this.$t('package.link_package_change');
            },
            buttonVariant() {
                if (this.isActivePackage) {
                    return 'secondary';
                }
                return 'outline-primary';
            },
            onSubmit() {
                window.scrollTo(0,275);
                this.isLoading = true;
                axios.put(this.packageChangeRoute, { 'id': this.packageId })
                    .then((response) => {
                        eventHub.$emit('selected-package:change', this.packageId);
                        eventHub.$emit('show-message:success', response.data.message);
                    })
                    .catch((error) => {
                        this.showError(error);
                    }).then(() => {
                        this.isLoading = false;
                    });
            }
        },

        created() {
            eventHub.$on("selected-package:change", packageId => {
                if (packageId > this.selectedPackageId) {
                    this.selectedPackageId = packageId;
                }
            });
        },
    }
</script>
