<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                <div class="card card-default">
                    <div class="card-header">Private Chat App</div>
                    <ul class="list-group">
                        <li class="list-group-item" v-for="friend in friends" :key="friend.id" @click="openChat(friend)">
                            {{friend.name}}
                            <span class="text-danger" v-if="friend.session && friend.session.unreadCount > 0">{{friend.session.unreadCount}}</span>
                            <i class="fa fa-circle pull-right" style="color:green;margin-top:4px;" v-if="friend.online"></i>
                            </li>
                        
                    </ul>
            </div>
        </div>
            <div class="col-md-9">
                
                <div class="col-9">
                    <span v-for="friend in friends" :key="friend.id" v-if="friend.session">
                        <app-message v-if="friend.session.open" @openClose="close(friend)" :friend="friend"></app-message>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Message from './Message';
    export default {
        data() {
            return {
                friends:[],
                sound:'http://soundbible.com/mp3/poker-chips-daniel_simon.mp3'
            }
        },
        components:{
            appMessage : Message
        },
        methods: {
            close(friend) {
                friend.session.open = false;
            },
            openChat(friend){
                if(friend.session)
                {
                    let sessionFriends = this.friends.filter(e => e.session != null);
                    
                     sessionFriends.forEach(e => e.session.open = false);
                     
                     friend.session.open = true;
                     friend.session.unreadCount = 0;

                }
                else
                {
                    this.createSession(friend);
                }
            },
            createSession(friend){
                axios.post('/session/create',{friend_id:friend.id}).then(res => {
                    friend.session = res.data.data;
                    friend.session.open = true;
                    
                });
            },
            getFriends(){
                axios.post('/getFriends').then(res => {
                    this.friends = res.data.data;
                    this.friends.forEach(friend =>{
                        if(friend.session)
                        {
                            this.listenForEverySession(friend);
                        }
                    });
                })
                .catch(err => console.log(err.response.data));
            },
            listenForEverySession(friend){
                Echo.private('Chat.' + friend.session.id).listen('PrivateEvent',(e)=>{
                    if(!friend.session.open) {
                        this.playSound();
                        friend.session.unreadCount++;
                    }
                });
            },
            playSound(){
                let alert = new Audio(this.sound);
                alert.play();
            }
        },
        created(){
            this.getFriends();


            Echo.channel('Chat')
            .listen('SessionEvent',e=>{
                console.log(e);
                let friend = this.friends.find(friend => friend.id == e.session_by);
                friend.session = e.session;
                this.listenForEverySession(friend);
            });

            Echo.join('Chat')
            .here(users => {
                this.friends.forEach(friend => {
                    users.forEach(onlineUser => {
                        if(onlineUser.id == friend.id){
                            friend.online = true;
                        }
                    })
                })
            })
        .joining((user) => {
            this.friends.forEach( e => user.id == e.id ? e.online = true : e.online = e.online);
        })
        .leaving((user) => {
             this.friends.forEach( e => user.id == e.id ? e.online = false : e.online = e.online);
        });
        }
    }
</script>

<style scoped>
.list-group-item:hover
{
    cursor:pointer;
    background:#eee;
}
</style>