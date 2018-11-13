<template>
    <div>
        <div class="card card-default chat-box">
            <div class="card-header">
                <b :class="{'text-danger':isBlock}">
                    {{isBlock ? 'You are blocked' : friend.name}}
                </b>
                <i class="fa fa-times pull-right" @click="close()" style="margin-top:3px;cursor:pointer;"></i>

                <div class="dropdown pull-right mr-4">
                    
                    <i class="fa fa-ellipsis-v"  id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="#" @click.prevent="block()">{{isBlock ? 'Unlock' : 'Block' }}</a>
                        <a class="dropdown-item" href="#" @click.prevent="clear()">Clear Chat</a>
                    </div>
                </div>
                
            </div>
            <div class="card-body" v-chat-scroll>
                <div class="card-text" v-for="chat in chats" :key="chat.id">
                    <p :class="{'pull-left' : chat.type,'pull-right' : !chat.type}" v-if="chat.messages.content">{{ chat.messages.content }}</p>
                    <div class="clearfix"></div>
                </div>
            </div>
            <form class="card-footer" @submit.prevent="send()">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Write your message" :disabled="isBlock" v-model="message">
                </div>
                
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        props:['friend'],
        data() {
            return {
                chats:[],
                isBlock:false,
                message:null
            }
        },
        methods: {
            
            close(){
                this.$emit('openClose');
            },
            clear(){
                this.chats = [];
            },
            block(){
                this.isBlock = !this.isBlock;
            },
            send()
            {
                if(this.message){
                    
                    axios.post(`send/${this.friend.session.id}`,{message:this.message,to_user:this.friend.id})
                    .then(res => {
                        this.chats.push(res.data.data[0]);
                        this.message = '';
                    });
                }
            },
            getMessages(){
                axios.post(`/send/${this.friend.session.id}/chats`)
                .then(res => {
                    this.chats = res.data.data;
                })
            },
            read()
            {
                axios.post(`/session/${this.friend.session.id}/read`)
            }
        },
        created() {
            this.getMessages();
            this.read();
            Echo.private('Chat.' + this.friend.session.id).listen('PrivateEvent',(e)=>{
                this.read();
                this.chats.push(e.chat);
            });
        },
    }
</script>

<style scoped>

.chat-box{ height:400px;}
.card-body { overflow-y: scroll;}
</style>