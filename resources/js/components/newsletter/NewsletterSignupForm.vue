<template>
    <div>
        <b-form
            ref="form"
            role="form"
            v-show="!isSuccess"
            @submit.stop.prevent="handleSubmit"
            @keydown="form.onKeydown($event)">
            <b-form-group
                :label="label"
                :label-sr-only="!showLabel"
                label-for="email"
            >
                <b-input
                    v-model="form.email"
                    :placeholder="placeholder"
                    :size="inputSize"
                    name="email"
                    type="email"
                    id="email"
                    required
                ></b-input>
            </b-form-group>
            <b-button
                class="float-right"
                :variant="buttonColor"
                :size="buttonSize"
                type="submit">{{ buttonLabel }}</b-button>
        </b-form>
        <b-alert variant="success" :show="isSuccess" class="m-3">
            {{ successMessage }}
        </b-alert>
    </div>
</template>

<script>
import Form from "vform";

export default {
    name: "NewsletterSignupForm",

    data() {
        return {
            form: new Form({
                email: '',
                tags: this.tags
            }),
            isSuccess: false
        }
    },

    props: {
        label: {
            type: String,
            default: function() { return this.$t('newsletter.label_signup'); },
            description: "Label"
        },
        placeholder: {
            type: String,
            default: function() { return this.$t('newsletter.placeholder_email'); },
            description: "Platzhalter"
        },
        successMessage: {
            type: String,
            default: function() { return this.$t('newsletter.success_message'); },
            description: "Erfolgsmeldung"
        },
        url: {
            type: String,
            required: true,
            description: "Newsletter Formular Link"
        },
        buttonColor: {
            type: String,
            default: 'primary'
        },
        buttonSize: {
            type: String,
            default: 'md'
        },
        buttonLabel: {
            type: String,
            default: function() { return this.$t('newsletter.button_submit'); },
        },
        inputSize: {
            type: String,
            default: 'md'
        },
        showLabel: {
            type: Boolean,
            default: false
        },
        tags: {
            type: String,
            default: null
        }
    },

    methods: {
        getValidationState({ dirty, validated, valid = null }) {
            return dirty || validated ? valid : null;
        },
        handleSubmit(event) {
            event.preventDefault();
            // Exit when the form isn't valid
            const valid = this.$refs.form.checkValidity();

            if (!valid) {
                return;
            }
            // Hide the modal manually
            this.$nextTick(() => {
                this.signUp();
            })
        },
        signUp() {
            this.isLoading = true;
            try {
                this.form.post(this.url)
                    .then((response) => {
                        //this.showMessage(response);
                        this.isSuccess = true;
                    })
                    .catch((error) => {
                        this.showError(error);
                    }).then(() => {
                        this.isLoading = false;
                    });
            } catch (e) {
                this.showError(e);
                this.isLoading = false;
            }
        }
    },

    mounted() {
    }

}
</script>

<style scoped>

</style>
