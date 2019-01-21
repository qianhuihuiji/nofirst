<script>
    import Replies from '../components/Replies';
    import SubscribeButton from '../components/SubscribeButton';

    export default {
        props: ['dataRepliesCount'],

        components: { Replies,SubscribeButton },

        data() {
            return {
                repliesCount:this.dataRepliesCount,
                locked:this.dataLocked
            }
        }
    }
</script>