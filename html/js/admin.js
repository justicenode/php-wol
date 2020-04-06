let servers = [];
let create = true;
let serverName, mac, ip, id, broadcast, username, password, usernameDisplay, level;

function getServers(){
	$.post("servers.php", {action:"get"}).done((servers) => {
		const serverList = $("#serverlist");
		serverList.html("");
		for (const server in servers) {
			const thisServer = servers[server];
			const row = $(`<tr><td>${thisServer.name}</td><td>${thisServer.ip}</td><td></td><td></td></tr>`);
			const col = $('<div class="btn-group"/>');
			col.append('<a class="btn btn-primary"><i class="glyphicon glyphicon-off"></i> Wake Up</a>').children().click(() => {wake(thisServer);});
			col.append($('<a class="btn btn-default"><i class="glyphicon glyphicon-refresh"></i> Refresh</a>').click(() => {ping(thisServer);}));

			if (level > 1) {
				const btngrp = $('<div class="btn-group"><a class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a><ul class="dropdown-menu" role="menu"></ul></div>');

				btngrp.children("ul").append($('<li><a><i class="glyphicon glyphicon-pencil"></i> Edit</a></li>').click(() => {edit(thisServer);}));
				btngrp.children("ul").append($('<li><a><i class="glyphicon glyphicon-trash"></i> Remove</a></li>').click(() => {remove(thisServer);}));

				col.append(btngrp);
			}

			row.children(':last').append(col);
			serverList.append(row);
			thisServer.statusField = $("#serverlist tr:last td:nth-child(3)");
		}

		pingAll();
	});
}

function guid() {
    const S4 = () => (((1+Math.random())*0x10000)|0).toString(16).substring(1);
    return (S4()+S4()+"-"+S4()+"-"+S4()+"-"+S4()+"-"+S4()+S4()+S4());
}

function addServer() {
	serverName.val("");
	mac.val("");
	ip.val("");
	broadcast.val("");
	id = guid();
	create = true;
	$("#modal").modal('show');
}

function edit(server) {
	serverName.val(server.name);
	mac.val(server.mac);
	ip.val(server.ip);
	broadcast.val(server.broadcast);
	id = server.id;
	create = false;
	$("#modal").modal('show');
}

function save() {
	$.post("servers.php", {id:id, mac:mac.val(), ip:ip.val(),name:serverName.val(),broadcast:broadcast.val(), action: (create ? "add" : "modify")})
		.done(() => {
			bootbox.alert("Server Saved");
			getServers();
		})
		.fail(function(data){
			bootbox.alert("error:" + data.status);
		});
}

function remove(server) {
	bootbox.confirm("Are you shure you want to delete \"" + server.name + '"', function(result){
		if(result){
			$.post("servers.php", {id:server.id, action:"remove"})
				.done(function(data){
					bootbox.alert("Server deleted");
					getServers();
				})
				.fail(function(data){
					bootbox.alert("error:" + data.status);
				});
		}
	});
}

function wake(server) {
	$.post("wake.php", {mac:server.mac, broadcast: server.broadcast})
		.done(function(data){
			bootbox.alert("Server woken");
			ping(server);
		})
		.fail(function(data){
			bootbox.alert("error:" + data.status);
		});
}

function ping(server){
	server.statusField.html("Loading...");
	server.statusField.css({color:"blue"});

	$.post("ping.php", {ip:server.ip})
		.done(function(data){
			if(data.response == "alive"){
				server.statusField.html("Alive");
				server.statusField.css({color:"green"});
			}
			if(data.response == "dead"){
				server.statusField.html("Dead");
				server.statusField.css({color:"red"});
			}
		})
		.fail(function(data){
			server.statusField.html("&lt;error&gt;");
			server.statusField.css({color:"orange"});
		});
}

function pingAll(){
	servers.forEach(server => ping(server));
}

function logout(){
	$.post("login.php", {a:"logout"})
		.done(function(){
			usernameDisplay.html("");
			level = 0;
			showLogin(true);
		});
}

function login(){
	$.post("login.php", {a:"login", username:username.val(), password:password.val()})
		.done(function(data){
			if(data.response){
				showLogin();
			}
			else {
				showLogin(true);
			}
		})
		.fail(function(data){
			bootbox.alert("Error");
			console.log(data);
		});
}

function showLogin(nocheck = false){
	if(nocheck){
		$("#login-modal").modal('show');
		return;
	}
	$.post("login.php", {a: "status"})
		.done(function(data){
			if (data.response == null){
				$("#login-modal").modal('show');
			}
			else {
				usernameDisplay.html(data.response.username);
				level = data.response.level;
				$('.require-level-2').css({display:(level >= 2)});
				$('.require-level-1').css({display:(level >= 1)});
				$('.require-level-3').css({display:(level >= 3)});
				getServers();
			}
		});
}

window.onload = function(){
	level = 0;
	serverName = $("#name");
	mac = $("#mac");
	ip = $("#ip");
	username = $("#username");
	broadcast = $("#broadcast");
	usernameDisplay = $('#username-display');
	password = $('#password');

	showLogin();
};