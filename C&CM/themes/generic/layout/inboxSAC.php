<?php $myInfo = (isset($_SESSION['user'])) ? $_SESSION['user'] : null; ?>
<div class="" id="micuenta-inbox">
	<div class="page-title">
		<div class="title_left">
			<h3>SAC <small>Bandeja de entrada</small></h3>
		</div>
	</div>
	<div class="clearfix"></div>
	
	<div class="row">
		<div class="col-md-12">
			<div class="x_panel">
				<div class="x_content">
					<div class="row">
						<div class="col-sm-3 mail_list_column">
							<!-- // <button id="compose" class="btn btn-sm btn-success btn-block" type="button">Redactar</button> -->
							
							<template v-if="records.length == 0">
								<a href="#">
									<div class="mail_list">
										<div class="left">
											
										</div>
										<div class="right">
											<h3> <small></small></h3>
											<p>No hay conversaciones.</p>
										</div>
									</div>
								</a>
							</template>
							<template v-else>
								<template v-for="(inbox, i) in records">
									<router-link tag="a" :to="{ name: 'MiCuenta-Inbox-Conversation-View', params: { conversation_id: inbox.id }}">
										<div class="mail_list">
											<div class="left">
												<i class="fa fa-circle" v-if="inbox.status.id == 1 || inbox.status.id == 0"></i> 
												<i class="fa fa-circle-o" v-else></i> 
												<!-- // <i class="fa fa-edit"></i> -->
											</div>
											<div class="right">
												<h3>
													<template v-if="inbox.conversations_replys[0]">
														{{ inbox.conversations_replys[0].user.names }} {{ inbox.conversations_replys[0].user.surname }}
													</template>
													<small>{{ inbox.updated }}</small>
												</h3>
												<!--
													{{ inbox }}
												<p v-html="inbox.conversations_replys[0].reply.text.replace(/<\/?[^>]+(>|$)/g, '').slice(0,50) + '...'"></p> 
												-->
											</div>
										</div>
									</router-link>
								</template>
							</template>
						</div>
						
						<div class="col-sm-9 mail_view">
							<router-view :key="$route.fullPath"></router-view>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<template id="micuenta-inbox-home">
	<div>
		
	</div>
</template>
		

<template id="micuenta-inbox-conversations-view">
	<div>
		<div class="inbox-body">
			<div class="mail_heading row">
				<div class="col-md-8">
					<div class="btn-group">
						<button @click="toggleMessage()" class="btn btn-sm btn-primary" type="button">
							<i class="fa fa-reply"></i> Responder
						</button>
						<!-- //
						<button disabled="" class="btn btn-sm btn-primary" type="button">
							<i class="fa fa-reply"></i> 
							Terminar Conversacion
						</button>
						-->
						<button disabled="" class="btn btn-sm btn-default" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Eliminar"><i class="fa fa-trash-o"></i></button>
					</div>
				</div>
				<div class="col-md-4 text-right">
					<p class="date"> Fecha de creacion: {{ $root.convertDate(conversation.created) }}</p>
				</div>
				<div class="col-md-12">
					<h4> Estado Actual: {{ conversation.status.name }}</h4>
					<!-- // <h4> Donec vitae leo at sem lobortis porttitor eu consequat risus. Mauris sed congue orci. Donec ultrices faucibus rutrum.</h4> -->
				</div>
			</div>

			<div class="col-md-12">
				<h4> Participantes: </h4>
				<div class="row">
					<div class="col-xs-3 profile_details" v-for="(conversation, m) in conversation.conversations_groups">
						<div class="well profile_view">
							<div class="col-sm-12">
								<h4 class="brief"><i>{{ conversation.user.username }}</i></h4>
								<div class="left col-xs-7">
									<h2>{{ conversation.user.names }} {{ conversation.user.surname }}</h2>
								</div>
								<div class="right col-xs-5 text-center">
									<!-- // <img :src="urlPictureById(conversation.user.avatar)" alt="" class="img-circle img-responsive"> -->
									<img src="https://d2ln1xbi067hum.cloudfront.net/assets/default_user-951af10295a22e5f7fa2fa6165613c14.png" alt="" class="img-circle img-responsive">
								</div>
							</div>
							<div class="col-xs-12 bottom text-center">
								<div class="col-xs-12 col-sm-6 emphasis">
									
								<!--
									<p class="ratings">
										<a>4.0</a>
										<a href="#"><span class="fa fa-star"></span></a>
										<a href="#"><span class="fa fa-star"></span></a>
										<a href="#"><span class="fa fa-star"></span></a>
										<a href="#"><span class="fa fa-star"></span></a>
										<a href="#"><span class="fa fa-star-o"></span></a>
									</p>	
								-->
								</div>
								<div class="col-xs-12 col-sm-12 emphasis">
									{{ conversation.conversations_groups }}
									<button type="button" class="btn btn-primary btn-xs" data-placement="top" data-toggle="tooltip" data-original-title="Enviar Privado">
										<i class="fa fa-comments-o"></i>
									</button>
									<button type="button" class="btn btn-danger btn-xs" data-placement="top" data-toggle="tooltip" data-original-title="Retirar de la conversacion">
										<i class="fa fa-times"></i>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		
			
					
			
				
			<template>
				<div class="x_content">
					<!-- start accordion  in -->
					<div class="accordion" id="accordion_" role="tablist" aria-multiselectable="true">
						<div class="panel" v-for="(item, a) in conversation.replys" :key="item.id">
							<template v-if="item.user.id === user_id">
								<a class="panel-heading" role="tab" 
									:id="'headingOne' + item.id" 
									data-toggle="collapse" 
									data-parent="#accordion" 
									:data-target="'#collapseOne' + item.id" 
									aria-expanded="false" 
									:aria-controls="'collapseOne' + item.id">
										<h4 class="panel-title text-right">
											Tú ({{ $root.convertDate(item.created) }})
											<i class="fa fa-chevron-down"></i>
										</h4>
								</a>
							</template>
							<template v-else>
								<a class="panel-heading bg-green" role="tab" 
									:id="'headingOne' + item.id" 
									data-toggle="collapse" 
									data-parent="#accordion" 
									:data-target="'#collapseOne' + item.id" 
									aria-expanded="false" 
									:aria-controls="'collapseOne' + item.id">
										<h4 class="panel-title text-left">
											<i class="fa fa-chevron-down"></i>
											<span>{{ item.user.names }} {{ item.user.surname }}</span> ({{ $root.convertDate(item.created) }})
										</h4>
								</a>
							</template>
							<div :id="'collapseOne' + item.id" :class="((a+1)==conversation.replys.length) ? 'panel-collapse collapse in multi-collapse' : 'panel-collapse collapse multi-collapse'" role="tabpanel" :aria-labelledby="'headingOne' + item.id">
								<div class="panel-body">
									<div class="sender-info">
										<div class="row">
											<div class="col-md-12">
												<strong>{{ item.user.names }} {{ item.user.names }}</strong>
												<span>( {{ item.user.username }} )</span> to
												<strong>me</strong>
												<a class="sender-dropdown"><i class="fa fa-chevron-down"></i></a>
											</div>
											<hr>
										</div>
									</div>
									
									<template v-if="item.reply.text">
										<div class="view-mail">
											<div v-html="item.reply.text"></div>
										</div>
									</template>
									<template v-if="item.reply.pictures">
										<div class="attachment">
											<p>
												<span><i class="fa fa-paperclip"></i> {{ item.reply.pictures.length }} Imágenes </span>
												<!-- //  <a href="#">Download all attachments</a> | <a href="#">View all images</a> -->
											</p>
											<ul>
												<li v-for="(pictureId) in item.reply.pictures">
													<a href="#" class="atch-thumb">
														<img :src="urlPictureById(pictureId)" alt="img" />
													</a>
													<!-- // <div class="file-name"> image-name.jpg </div> -->
													<!-- // <span>12KB</span> -->
													<!-- // <div class="links"><a href="#">View</a> - <a href="#">Download</a></div> -->
												</li>
											</ul>
										</div>
									</template>
									
								</div>
							</div>
						</div>
					</div>
					<!-- end of accordion -->
				</div>
			
				<div class="btn-group">
					<button @click="toggleMessage()" class="btn btn-sm btn-primary" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Responder"><i class="fa fa-reply"></i> </button>
					
					
					<!-- //
					<button class="btn btn-sm btn-default" type="button"  data-placement="top" data-toggle="tooltip" data-original-title="Forward"><i class="fa fa-share"></i></button>
					<button class="btn btn-sm btn-default" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Print"><i class="fa fa-print"></i></button>
					<button class="btn btn-sm btn-default" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Trash"><i class="fa fa-trash-o"></i></button>
					-->
				</div>
				
				<!-- // {{ conversation }} -->
				<div class="ln_solid"></div>
				
				<div class="x_content" v-if="enableMessage == true">
					<textarea rows="6" v-model="me.compose.text" class="form-control"></textarea>
						
					<div class="btn-group pull-right">
						<!-- // <button v-if="conversation.status.id == 0 || conversation.status.id == 2" class="btn btn-sm btn-primary" type="button"><i class="fa fa-reply"></i> </button> -->
						
							<template v-if="enableMessage == true">
								<button @click="toggleMessage()" class="btn btn-sm btn-default" type="button">
									<i class="fa fa-times"></i> Cerrar
								</button>
							</template>
							
							
						<button @click="sendMessage" class="btn btn-sm btn-success" type="button"  data-placement="top" data-toggle="tooltip" data-original-title="Forward"><i class="fa fa-share"></i> Enviar Mensaje</button>
						<!-- // <button class="btn btn-sm btn-default" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Trash"><i class="fa fa-trash-o"></i></button> -->
					</div>
				</div>
			</template>
		</div>
		
		
		<div>
		</div>
	</div>
</template>

<script>
var InboxHome = Vue.extend({
	template: '#micuenta-inbox-home',
	data: function () {
		return {
		};
	},
	methods: {
	}
});

var InboxConversationsView = Vue.extend({
	template: '#micuenta-inbox-conversations-view',
	data: function () {
		return {
			user_id: <?php echo (isset($myInfo['id'])) ? $myInfo['id'] : 0; ?>,
			conversation: {
				id: '',
				created: '',
				status: {
					id: '',
					name: ''
				},
				updated: '',
				replys: [],
			},
			me: {
				avatar: "https://lh6.googleusercontent.com/-lr2nyjhhjXw/AAAAAAAAAAI/AAAAAAAARmE/MdtfUmC0M4s/photo.jpg?sz=48",				
				compose: {
					text: '',
				},
			},
			you: {
				avatar: "https://a11.t26.net/taringa/avatares/9/1/2/F/7/8/Demon_King1/48x48_5C5.jpg"
			},
			enableMessage: false,
		};
	},
	methods: {
		urlPictureById(id){
			return (id !== null) ? "https://a11.t26.net/taringa/avatares/9/1/2/F/7/8/Demon_King1/48x48_5C5.jpg" : "/index.php?controller=Sistema&action=picture&id=" + id;
		},
		validateResultConversation(r){
			// console.log('validateResultConversation', r)
			
			var self = this;
			if (r.data != undefined){
				// console.log(r.data);
				self.conversation.id = r.data.id;
				self.conversation.created = r.data.created;
				self.conversation.status = r.data.status;
				self.conversation.updated = r.data.updated;
				self.conversation.conversations_groups = r.data.conversations_groups;
				r.data.conversations_replys.forEach(function(rp){
					rp.reply = JSON.parse(rp.reply);
				});
				self.conversation.replys = r.data.conversations_replys;
			} else {
				 // console.log('Error: consulta validateResultConversation'); 
				 // console.log(response); 
			}
		},
		showErrorAlert(reason, detail){
          var msg = '';
          if (reason === 'unsupported-file-type') {
            msg = "Unsupported format " + detail;
          } else {
            // console.log("error uploading file", reason, detail);
          }
          $('<div class="alert"> <button type="button" class="close" data-dismiss="alert">&times;</button>' +
            '<strong>File upload error</strong> ' + msg + ' </div>').prependTo('#alerts');
		},
		sendMessage(){
			var self = this;
			obj_message = {};
			
			if(self.me.compose.text != ''){
				// console.log('enviar');
				obj_message.reply = JSON.stringify({ "text": self.me.compose.text });
				obj_message.conversation = Number(self.$route.params.conversation_id);
				obj_message.user = self.user_id;
				// console.log(obj_message);
				api.post('/records/conversations_replys/', obj_message)
				.then(response => {
					// console.log(response);
					self.me.compose.text = '';
					api.put('/records/conversations/' + self.conversation.id, {
						status: 3
					})
					.then(response => {
						// console.log("Conversacion actualizanda.");
						// console.log("response: ", response);
						self.load();
						// Validar que el `user` este en la conversacion
						api.get('/records/conversations_groups', {
							params: {
								filter: [
									'user,eq,' + obj_message.user,
									'conversation,eq,' + obj_message.conversation
								]
							}
						})
						.then(abi => {
							console.log('abi', abi);
							if(abi.data != undefined && abi.data.records != undefined && abi.data.records.length == 0){
								api.post('/records/conversations_groups', {
									user: obj_message.user,
									conversation: obj_message.conversation
								})
								.then(r_abi => {
									console.log("r_abi", r_abi);
								})
								.catch(e => {
									// console.log("Error actualizando la conversacion.");
								})
							}
						});
					})
					.catch(e => {
						// console.log("Error actualizando la conversacion.");
					})
					
				})
				.catch(e => {
					// console.log("Error");
					// console.log(e.response);
				});
			}
		},
		load(){
			var self = this;
			conversation_id = (!self.$route.params.conversation_id) ? 0 : self.$route.params.conversation_id;
			
			api.get('/records/conversations/' + conversation_id, {
				params: {
					filter: [],
					join: [
						'conversations_status',
						'conversations_replys',
						'conversations_replys,users',
						'conversations_groups',
						'conversations_groups,users',
					]
				}
			})
			.then(response => { self.validateResultConversation(response); })
			.catch(e => { self.validateResultConversation(e); });
		},
		fetchEventsList() {
			var self = this;
			self.load();
		},
		cancelAutoUpdate(){
			var self = this;
			clearInterval(self.timer);
		},
		toggleMessage(){
			var self = this;
			self.enableMessage = !self.enableMessage;
		}
	},
	created(){
		var self = this;
		self.fetchEventsList();
		self.timer = setInterval(self.fetchEventsList, 5000); // 3000 = 3Sec --- 300000
	},
	mounted(){
		var self = this;
	},
});

var router = new VueRouter({
	linkActiveClass: 'active',
	routes:[
		{ path: '/', component: InboxHome, name: 'MiCuenta-Inbox' },
		{ path: '/conversation/:conversation_id/view', component: InboxConversationsView, name: 'MiCuenta-Inbox-Conversation-View' },
	]
});


var Inbox = new Vue({
	router: router,
	data(){
		return {
			count: 0,
			records: []
		};
	},
	mounted(){
		var self = this;
		self.load();
	},
	methods: {
		load(){
			var self = this;
			api.get('/records/conversations', {
				params: {
					filter: [
						'sac,eq,1'
						// 'status,in,0,1'
						// 'conversations.status,eq,2'
					],
					join: [
						// 'conversations',
						// 'conversations,conversations_replys',
						// 'conversations,conversations_replys,users',
					],
					order: 'id,desc'
				}
			})
			.then(response => { self.validateResult(response); })
			.catch(e => { self.validateResult(e.response); });
		},
		getLink(pqrs){
			var self = this;
			action = '';
			pqrs.id = (pqrs.id != undefined && pqrs.id > 0) ? pqrs.id : 0;
			typeId = (pqrs.type.id != undefined && pqrs.type.id > 0) ? pqrs.type.id : ((pqrs.type != undefined && pqrs.type > 0) ? pqrs.type : 0);
			return '/index.php?controller=PQRSF&action=ver_pqrsf&type=' + typeId + '&id=' + pqrs.id;
		},
		validateResult(a){
			var self = this;
			try{
				if (a.data != undefined && a.data.records != undefined){
					self.conversations = [];
					a.data.records.forEach(item => {
						self.conversations.push(item.id);
					});
					if(self.conversations.length > 0){
						api.get('/records/conversations', {
							params: {
								filter: [
									'id,in,' + self.conversations.join(',')
								],
								join: [
									'users',
									'conversations_status',
									'conversations_replys',
									'conversations_replys,users'
								],
								order: 'updated,desc'
							}
						})
						.then(response => { self.validateConversations(response); })
						.catch(e => { self.validateConversations(e.response); });
					}
				}
			}catch(e){
				// console.log(e);
				// console.log(e.response);	
			};
		},
		validateConversations(response){
			var self = this;
			self.records = [];
			self.count = 0;
			try{
				if (response.data != undefined){
					// console.log('item: response;', response);
					if (response.data.records.length > 0){
						response.data.records.forEach(item => {
							if(item.conversations_replys.length > 0){
								item.conversations_replys.forEach(function(a){
									a.reply = JSON.parse(a.reply);
								});
								item.updated = new Date(item.updated).toConversationsFormat();
								if (item.status === 0 || item.status === 1){ self.count++; }
								self.records.push(item);
							}else{
								// console.log('item', item);
							}
						});
					} else {
						self.searchBox.errorText = "No hay mensajes";
					}
				} 
			}catch(e){
				// console.log(response);
				// console.log(e);
				// console.log(e.response);	
			};
			
		},
		validateMessages(response){
			var self = this;
			self.records = [];
			self.count = 0;
			
			if (response.data != undefined){
				if(response.data.records.length > 0){
					response.data.records.forEach(item => {
						item.conversations_replys.forEach(function(a){
							a.reply = JSON.parse(a.reply);
						});
						item.updated = new Date(item.updated).toConversationsFormat();
						if(item.status === 2){ self.count++; }
						self.records.push(item);
					});
				}
			} else {
				 // console.log('Error: consulta'); 
				 // console.log(response.data); 
			}
		},
		convertDate(date){
			return new Date(date).toConversationsFormat();
		}
	},
}).$mount('#micuenta-inbox');

</script>

