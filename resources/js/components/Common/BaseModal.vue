<template>
    <transition name="modal-fade">
        <div @click="close" class="modal-backdrop">
            <div class="modal show d-block" tabindex="-1" role="dialog">
                <div @click.stop class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <slot name="header">
                                    Modal
                                </slot>
                            </h5>
                            <button @click="close" type="button" class="close" aria-label="Close modal">x</button>
                        </div>
                        <div class="modal-body">
                            <slot name="body">
                                Modal content
                            </slot>
                        </div>
                        <div class="modal-footer">
                            <slot name="footer">
                                <button @click="close" type="button" class="btn-green" aria-label="Close modal">
                                    Close
                                </button>
                            </slot>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script type="text/javascript">
export default {
    methods: {
        close () {
            this.$emit('close');
        },
        checkKeyInModal(event) {
            if (event.keyCode === 27) {
                this.$emit('close');
            }
        }
    },

    mounted() {
        document.addEventListener('keyup', this.checkKeyInModal);
    },

    destroyed() {
        document.removeEventListener('keyup', this.checkKeyInModal);
    }
}
</script>

<style>
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.3);
}

.modal-fade-enter,
.modal-fade-leave-active {
    opacity: 0;
}

.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity .05s ease
}
</style>
