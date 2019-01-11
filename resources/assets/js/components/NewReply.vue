<template>
    <div>
        <div v-if="signedIn">
            <div class="form-group">
                <textarea name="body"
                        id="body"
                        class="form-control"
                        placeholder="说点什么吧..."
                        rows="5"
                        required
                        v-model="body"></textarea>
            </div>

            <button type="submit" class="btn btn-default" @click="addReply">
                提交
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                body: '',
            }
        },

        computed: {
            signedIn() {
                return window.App.signIn;
            }
        },

        methods: {
            addReply() {
                axios.post(location.pathname + '/replies',{ body:this.body })
                    .then(({data}) => {
                        this.body = '';

                        flash('Your Reply has been posted.');

                        this.$emit('created',data);
                    });
            }
        }
    }
</script>
