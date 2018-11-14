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
                    <p :class="{'pull-left' : chat.type,'pull-right' : !chat.type,'text-success':chat.read_at != ''}" v-if="chat.messages.content">{{ chat.messages.content }}</p>
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
                        console.log('message',res.data);
                        this.chats.push(res.data.data[0]);
                        this.chats[this.chats.length -1].id = res.data.data[0].id;
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
                this.friend.session.open ? this.read() : "";
                if(e.chat.user_id != e.to_user) e.chat.type = 1;
                this.chats.push(e.chat);
            });


            Echo.private('Chat.' + this.friend.session.id).listen('MsgReadEvent',(e)=>{
                this.chats.forEach(chat => chat.id == e.chat.id ? chat.read_at = e.chat.read_at : "");
            });
        },
    }
</script>

<style scoped>

.chat-box{ height:400px;}
.card-body { overflow-y: scroll;}
</style>