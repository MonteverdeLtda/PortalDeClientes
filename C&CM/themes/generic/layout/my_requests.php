<?php
$myInfo = $this->myUser;
?>
<div class="" id="micuenta-requests">
	<div class="page-title">
		<div class="title_left">
			<h3>Mi Cuenta <small> Mis Solicitudes</small></h3>
		</div>
	</div>
	<div class="clearfix"></div>

	<div class="row">
		<div class="col-md-12">
			<div class="x_panel">
				<div class="x_content">
					<div class="row">
						<div class="col-sm-12 mail_view">
							<router-view :key="$route.fullPath"></router-view>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<template id="micuenta-requests-list">
	<div>
		<table class="table table-striped projects">
		  <thead>
			<tr>
			  <th style="width: 1%">#</th>
			  <th style="width: 20%">Responsable</th>
			  <th>Team Members</th>
			  <th>Project Progress</th>
			  <th>Status</th>
			  <th>Type</th>
			  <th style="width: 20%">#Edit</th>
			</tr>
		  </thead>
			<tbody>
					<tr v-for="(item, i) in records">
						<td>{{ getRadicado(item.request) }}</td>
						<td>
							<a>{{ item.request.names }} {{ item.request.surname }}</a>
							<br />
							<small>{{ item.request.created }}</small>
						</td>
						<td>
							<ul class="list-inline">
								<li v-for="(member_team, i2) in item.request.requests_team">
									<small>{{ member_team.user.username }}</small>
									<img src="/C&CM/themes/generic/assets/images/default_user.png" class="avatar" alt="Avatar" />
								</li>
							</ul>
						</td>
						<td class="project_progress">
							<div class="progress progress_sm">
								<div class="progress-bar bg-green" role="progressbar" :data-transitiongoal="item.request.status.progress"></div>
							</div>
							<small>{{ item.request.status.progress }}% Completado</small>
						</td>
						<td>
							<button type="button" class="btn btn-success btn-xs">{{ item.request.status.name }}</button>
						</td>
						<td>
							<button type="button" class="btn btn-default btn-xs">{{ item.request.type.title }}</button>
						</td>
						<td>
							<router-link tag="a" :to="{ name: 'MiCuenta-Requests-View', params: { request_id: item.request.id } }" class="btn btn-primary btn-xs">
								<i class="fa fa-folder"></i> Ver mas
							</router-link>
							<!--
							<a href="#" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit </a>
							<a href="#" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> Delete </a>
							-->
						</td>
					</tr>
			</tbody>
		</table>
	</div>
</template>

<template id="micuenta-requests-view">
	<div>
		<div class="x_panel">
			<div class="x_title">
				<h2>{{ record.type.title }}</h2>
				<ul class="nav navbar-right panel_toolbox">
					<!-- // <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li> -->
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="#">Settings 1</a></li>
							<li><a href="#">Settings 2</a></li>
						</ul>
					</li>
					<router-link tag="li" :to="{ name: 'MiCuenta-Requests' }">
						<a class="close-link"><i class="fa fa-close"></i></a>
					</router-link>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="col-md-8 col-sm-8 col-xs-12">
					<ul class="stats-overview">
						<li>
							<span class="name"> Estimated budget </span>
							<span class="value text-success"> 2300 </span>
						</li>
						<li>
							<span class="name"> Total amount spent </span>
							<span class="value text-success"> 2000 </span>
						</li>
						<li class="hidden-phone">
							<span class="name"> Estimated project duration </span>
							<span class="value text-success"> 20 </span>
						</li>
					</ul>
					<br />
					<ul class="stats-overview">
						<li>
							<span class="name">
								Departamento / Ciudad
							</span>
							<span class="value text-success">
								{{ record.city.name }}, {{ record.department.name }}
							</span>
						</li>
						<li class="hidden-phone">
							<span class="name">Direccion </span>
							<span class="value text-success"> {{ record.address }} </span>
						</li>
						<li class="hidden-phone">
							<span class="name"> Puntos de referencia </span>
							<span class="value text-success"> {{ record.points_reference }} </span>
						</li>
					</ul>
					<br />
					<!-- // <div id="mainb" style="height:350px;"></div> -->
					<div>
						<div class="row">
							<div class="col-sm-12">
								<div class="x_content">
									<div class="row" role="tabpanel" data-example-id="togglable-tabs">
										<div class="col-sm-12">
											<ul id="myTab1" class="nav nav-tabs bar_tabs_ " role="tablist">
												<li role="presentation" class="active"><a href="#tab_content11" id="home-tabb" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true">Actividad Reciente</a></li>
												<li role="presentation" class=""><a @click="refreshCalendar" href="#tab_calendar" role="tab" id="calendar-tabb" data-toggle="tab" aria-controls="calendar" aria-expanded="false">Calendario</a></li>
												<li role="presentation" class=""><a href="#tab_content22" role="tab" id="profile-tabb" data-toggle="tab" aria-controls="profile" aria-expanded="false">Propuestas</a></li>
											</ul>
										</div>

										<div class="col-sm-12">
											<div id="myTabContent2" class="tab-content">
												<div role="tabpanel" class="tab-pane fade active in" id="tab_content11" aria-labelledby="home-tab">

													<ul class="messages">
														<template v-if="record.requests_activity.length > 0">
															<li v-for="activity in record.requests_activity">
																<!-- // <img src="images/img.jpg" class="avatar" alt="Avatar"> -->
																<div class="message_date">
																	<h3 class="date text-info">{{ record.created.split(" ")[0].split("-")[2] }}</h3>
																	<p class="month">{{ returnMouthText(record.created.split(" ")[0].split("-")[1]) }}</p>
																</div>
																<div class="message_wrapper">
																	<h4 class="heading">(@{{ activity.user.username }}) - {{ activity.user.names }}  {{ activity.user.surname }}</h4>
																	<blockquote class="message" v-if="activity.info.text != undefined">{{ activity.info.text }}</blockquote>
																	<br />
																	<template v-if="activity.type == 'attachment'">
																		<p class="url" v-for="attachment in activity.info.attachment">
																			<span class="fs1 text-info" aria-hidden="true" data-icon=""></span>
																			<!-- //
																			<a :href="attachment.path_short"><i class="fa fa-paperclip"></i> {{ attachment.name }} [ {{ attachment.size }} B ]</a>
																			<a :href="attachment.path_short"><i class="fa fa-paperclip"></i> {{ attachment.name }} [ {{ (attachment.size/1024) }} Kb ]</a>
																			<a :href="attachment.path_short"><i class="fa fa-paperclip"></i> {{ attachment.name }} [ {{ ((attachment.size/1024)/1024) }} Mb ]</a>
																			-->
																			<a target="_blank" :href="attachment.path_short"><i class="fa fa-paperclip"></i> {{ attachment.name }} </a>
																		</p>
																	</template>
																	<template v-else-if="activity.type == 'events'">
																		<ul>
																			<li  v-for="event in activity.info.events">
																				<span class="fs1 text-info fa fa-calendar-o" aria-hidden="true"></span>
																					{{ event.title }}
																					<br>Inicio: {{ event.start }}
																					<br>Fin: {{ event.end }}
																			</li>
																		</ul>
																	</template>
																</div>
															</li>
														</template>
														<template v-else>
															<li>
																<!-- // <img src="images/img.jpg" class="avatar" alt="Avatar"> -->
																<div class="message_date">
																	<h3 class="date text-info"></h3>
																	<p class="month"></p>
																</div>
																<div class="message_wrapper">
																	<h4 class="heading">Mensaje automatico del sistema</h4>
																	<blockquote class="message">
																		Esta solicitud necesita de tu gestión, Comencemos!
																	</blockquote>
																	<br />
																	<!--
																	<p class="url">
																		<span class="fs1 text-info" aria-hidden="true" data-icon=""></span>
																		<a href="#"><i class="fa fa-paperclip"></i> User Acceptance Test.doc </a>
																	</p>
																	-->
																</div>
															</li>
														</template>
													</ul>
												</div>
												<div role="tabpanel" class="tab-pane fade" id="tab_calendar" aria-labelledby="calendar-tab">
													<div id="calendar-list"></div>
													<template v-if="events.length == 0 || events == undefined || events == null">
														No se a programado agenta.
													</template>
												</div>



												<div role="tabpanel" class="tab-pane fade" id="tab_content22" aria-labelledby="profile-tab">
													<ul class="messages">
														<li>
															<!-- // <img src="images/img.jpg" class="avatar" alt="Avatar"> -->
															<div class="message_date">
																<h3 class="date text-info"></h3>
																<p class="month"></p>
															</div>
															<div class="message_wrapper">
																<h4 class="heading">Mensaje automatico del sistema</h4>
																<blockquote class="message">
																	Aún no tenemos propuestas, espera que nuestros especialistas analicen tu solicitud y realicen el estudio para enviarte tu propuesta.
																</blockquote>
																<br />
																<!--
																<p class="url">
																	<span class="fs1 text-info" aria-hidden="true" data-icon=""></span>
																	<a href="#"><i class="fa fa-paperclip"></i> User Acceptance Test.doc </a>
																</p>
																-->
															</div>
														</li>
													</ul>
												</div>
												<div role="tabpanel" class="tab-pane fade" id="tab_content33" aria-labelledby="profile-tab">
													<p>xxFood truck fixie locavore, accusamus mcsweeney's marfa nulla single-origin coffee squid. Exercitation +1 labore velit, blog sartorial PBR leggings next level wes anderson artisan four loko farm-to-table craft beer twee. Qui photo
														booth letterpress, commodo enim craft beer mlkshk </p>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<section class="panel">
						<div class="x_title">
							<h2>Especificaciones de la solicitud</h2>
							<div class="clearfix"></div>
						</div>
						<div class="panel-body">
							<h3 class="green"><i class="fa fa-paint-brush"></i> {{ record.status.name }}</h3>
							<p>{{ record.request }}</p>
							<br />
							<div class="project_detail">
								<p class="title">Tipo de documento</p>
								<p>{{ record.identification_type.name }}</p>
								<p class="title"># de documento</p>
								<p>{{ record.identification_type.code }} {{ record.identification_number }}</p>
								<p class="title">Nombres o Razón social</p>
								<p>{{ record.names }}</p>
								<p class="title">Apellidos</p>
								<p>{{ record.surname }}</p>
								<p class="title">Correo electronico</p>
								<p>{{ record.email }}</p>
								<p class="title">Teléfono Fijo</p>
								<p>{{ record.phone }}</p>
								<p class="title">Teléfono Móvil</p>
								<p>{{ record.mobile }}</p>

								<p class="title">Project Leader</p>
								<p>Tony Chicken</p>
							</div>
							<br />
							<h5>Archivos en la solicitud</h5>
							<ul class="list-unstyled project_files">
								<li><a href=""><i class="fa fa-file-word-o"></i> Functional-requirements.docx</a></li>
								<li><a href=""><i class="fa fa-file-pdf-o"></i> UAT.pdf</a></li>
								<li><a href=""><i class="fa fa-mail-forward"></i> Email-from-flatbal.mln</a></li>
								<li><a href=""><i class="fa fa-picture-o"></i> Logo.png</a></li>
								<li><a href=""><i class="fa fa-file-word-o"></i> Contract-10_12_2014.docx</a></li>
							</ul>
							<br />
							<div class="text-center mtop20">
								<!--
									<a href="#" class="btn btn-sm btn-primary">Add files</a>
									<a href="#" class="btn btn-sm btn-warning">Report contact</a>
								-->
							</div>
						</div>
					</section>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
var MyRequestsList = Vue.extend({
	template: '#micuenta-requests-list',
	data: function () {
		return {
			records: []
		};
	},
	mounted(){
		var self = this;
		self.load();

				// Progressbar
				$(document).ready(function() {
					console.log('ok');
					$('.progress .progress-bar').progressbar();
				});
	},
	methods: {
		zfill: zfill,
		getRadicado(item){
			var self = this;
			radSeparate = item.created.split(" ");
			radFecha = radSeparate[0].split("-");
			return radFecha[0] + radFecha[1] + radFecha[2] + self.zfill(item.id, 5);
		},
		load(){
			var self = this;
			api.get('/records/users_requests/', {
				params: {
					filter: [
						'user,eq,<?php echo (isset($myInfo->id)) ? $myInfo->id : 0; ?>'
					],
					join: [
						'requests',
						'requests,requests_types',
						'requests,requests_status',
						'requests,requests_team',
						'requests,requests_team,users'
					]
				}
			})
			.then(response => { self.validateResult(response); })
			.catch(e => { self.validateResult(e); });
		},
		validateResult(r){
			var self = this;
			self.records = [];
			if (r.data.records != undefined){
				// console.log(r.data);
				self.records = r.data.records;
			} else {
				 console.log('Error: consulta validateResult');
				 //console.log(response);
			}
		},
	}
});

var MyRequestsView = Vue.extend({
	template: '#micuenta-requests-view',
	data: function () {
		return {
			request_id: this.$route.params.request_id,
			record: {
			  "id": 0,
			  "type": {
				"id": 0,
				"title": "",
				"subtitle": "",
				"description": "",
				"highlight": 0
			  },
			  "identification_type": 0,
			  "identification_number": "",
			  "names": "",
			  "surname": "",
			  "department": 0,
			  "city": 0,
			  "address": "",
			  "points_reference": "",
			  "email": "",
			  "phone": "",
			  "mobile": "",
			  "request": "",
			  "status": {
				"id": 0,
				"name": "",
				"progress": 0,
				"close": 0
			  },
			  "created": "",
			  "updated": "",
			  "requests_team": [],
				"requests_activity": []
			},
			calendarEl: null,
			calendar: null,
			events: [],
		};
	},
	mounted(){
		var self = this;
		self.load();
	},
	methods: {
		refreshCalendar(){
			var self = this;
			if(self.events.length > 0){
				self.calendar.render();
			}
		},
		loadCalendar(){
			var self = this;
			self.calendarEl = document.getElementById('calendar-list');
			self.calendar = new FullCalendar.Calendar(self.calendarEl, {
				timeZone: 'UTC',
				lang: 'es',
				header: {
					left: 'listWeek,timeGridWeek',
					center: 'title',
					right: 'today prev,next',
				},
				height: 450,
				plugins: [ 'list', 'timeGrid' ],
				//defaultView: 'timeGridWeek',
				defaultView: 'listWeek',
				events: self.events
			});
		},
		loadReservations(){
			var self = this;
			api.get('/records/events', {
				params: {
					filter: [
						'request,eq,' + self.$route.params.request_id
					]
				}
			})
			.then(response => { self.validateResultCalendar(response); })
			.catch(e => { self.validateResultCalendar(e.response); });
		},
		validateResultCalendar(r){
			var self = this;
			console.log('r', r);
			if(r.data != undefined && r.data.records != undefined){
				self.events = r.data.records;
				self.loadCalendar();
			}
		},
		zfill: zfill,
		returnMouthText(mouth){
			array = [ 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' ];
			return array[mouth-1];
		},
		getRadicado(item){
			var self = this;
			radSeparate = item.created.split(" ");
			radFecha = radSeparate[0].split("-");
			return radFecha[0] + radFecha[1] + radFecha[2] + self.zfill(item.id, 5);
		},
		load(){
			var self = this;
			self.request_id = (!self.$route.params.request_id) ? 0 : self.$route.params.request_id;
			api.get('/records/users_requests/', {
				params: {
					filter: [
						'user,eq,<?php echo (isset($myInfo->id)) ? $myInfo->id : 0; ?>',
						'request,eq,' + self.request_id
					],
					join: [
						'requests',
						'requests,identifications_types',
						'requests,geo_departments',
						'requests,geo_citys',
						'requests,requests_types',
						'requests,requests_status',
						'requests,requests_team',
						'requests,requests_team,users',
						'requests,requests_activity',
						'requests,requests_activity,users',
					]
				}
			})
			.then(response => { self.validateResult(response); })
			.catch(e => { self.validateResult(e); });
		},
		validateResult(r){
			var self = this;
			if (r.data.records[0] != undefined){
				// console.log(r.data);
				r.data.records[0].request.requests_activity.forEach(function(x){
					x.info = JSON.parse(x.info);
				});
				self.record = r.data.records[0].request;
				self.loadReservations();
			} else {
				 console.log('Error: consulta validateResult');
				 //console.log(response);
			}
		},
	}
});

var router = new VueRouter({
	linkActiveClass: 'active',
	routes:[
		{ path: '/', component: MyRequestsList, name: 'MiCuenta-Requests' },
		{ path: '/view/:request_id', component: MyRequestsView, name: 'MiCuenta-Requests-View' },
	]
});


var MyRequests = new Vue({
	router: router,
	data(){
		return {
			count: 0,
			records: []
		};
	},
	mounted(){
		var self = this;
	},
	methods: {
	},
}).$mount('#micuenta-requests');

</script>
