<?php 
$maleSVG = file_get_contents(__DIR__.'/img/male.svg');
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
<title>Insert title here</title>
<link rel='stylesheet' href='lib/bootstrap/dist/css/bootstrap.min.css'>
<style>
	#sex .sex-img{
		cursor: pointer;
	}
	
	.topic-link{
		height: 100%;
		width: 100%;
	}

	.sex-col{
		height: 80%;
	}

	.sex-img{
		width: auto;
		height: 100%;
	}

	.fullscreen {
		height: 100vh;
		text-align: center;
	}
	
	.area{
		background-color: pink;
		position: absolute;
	}
	
	.body-part{
		z-index: 10;
		cursor: pointer;
	}

	g{
		pointer-events: bounding-box;
	}
</style>
</head>
<body>
<div class='container-fluid'>
	<div id='title' class='fullscreen'>
		<div class='row vcenter'>
			<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
				<h1>Title</h1>
			</div>
			<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
				<a href='#sex' class='btn btn-primary'>Enter</a>
			</div>
		</div>
	</div>
	<div id='sex' class='fullscreen'>
		<div class='row vcenter'>
			<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
				<h1>Select Your Sex:</h1>
			</div>
			<div class='sex-col col-lg-6 col-md-6 col-sm-6 col-xs-6'>
				<h1>Male</h1>
				<a href='#topic'>
					<img class='sex-img' data-sex='male' src='img/aiga-toilet-men.png'>
				</a>
			</div>
			<div class='sex-col col-lg-6 col-md-6 col-sm-6 col-xs-6'>
				<h1>Female</h1>
				<a href='#topic'>
					<img class='sex-img' data-sex='female' src='img/aiga-toilet-women.png'>
				</a>
			</div>
		</div>
	</div>
	<div id='topic' class='fullscreen'>
		<div class='row vcenter'>
			<h1>What area(s) are you interested in?</h1>
			<div class='sex-col col-lg-6 col-md-6 col-sm-6 col-xs-12'>
				<div id='male-body' class='human-body' style='display: none;'>
					<?php echo $maleSVG; ?>
				</div>
				<div id='female-body' class='human-body'>
					<img class='sex-img' data-sex='female' src='img/aiga-toilet-women.png'>
				</div>
				<button id='send-body-parts' class='btn btn-primary'>Go</button>
			</div>
			<div class='sex-col col-lg-6 col-md-6 col-sm-6 col-xs-12'>
				<h2>Topics</h2>
				<div id='topic-list'>
				</div>
			</div>
		</div>
	</div>
</div>
<script src='lib/jquery/dist/jquery.min.js'></script>
<script src='lib/bootstrap/dist/js/bootstrap.min.js'></script>
<script>
var bodyParts = [];

/* Define WS server connections */

//Connect to servers
var servers = {
	index: new WebSocket("ws://localhost:8765/"),
	question: new WebSocket("ws://localhost:8767/"),
	topic: new WebSocket("ws://localhost:8766/")
};

//Define index server interactions
servers.index.onmessage = function (event) {
    var topics = JSON.parse(event.data),
    	$list = $('#topic-list').empty();

    $(topics).each(function(idx, topic){
        var name = topic.topicname,
        	id = topic.topicid;

        $list.append("<div data-id='" + id + "'>" + name + "</div>");
    });
};

servers.index.onclose = function (event) {
	console.log('index server closed');
};

servers.index.onerror = function (event) {
	console.error(event);
};

servers.index.onopen = function (event) {
	console.log('index server open');
};

//Define question server interactions
servers.question.onmessage = function (event) {
    var answers = JSON.parse(event.data);
    document.getElementById('answers').innerHTML = JSON.stringify(answers);
};

//Define topic server interactions
servers.topic.onmessage = function (event) {
    var questions = JSON.parse(event.data);
    document.getElementById('questions').innerHTML = JSON.stringify(questions);
};

$(document).ready(function(){
	$('.sex-img').click(sexImg_Click);
	$('.body-part').click(bodyPart_Click);
	$('#send-body-parts').click(sendBodyParts_Click);

	$('.vcenter').each(function(idx, obj){
		vcenter($(obj));
	});
});

function vcenter($target){
	var pHeight = $target.parent().height(),
		tHeight = $target.height(),
		margin = pHeight / 2 - tHeight / 2;

	$target.css({
		'padding-top': margin
	});
}

function sendBodyParts_Click(e){
	debugger;
	servers.index.send(bodyParts);
}

function sexImg_Click(e){
	var $target = $(event.target),
		sex = $target.data('sex');
	
	$('.human-body').hide();
	$('#' + sex + '-body').show();

	topics = [];

	$('.body-part.selected').each(function(idx, target){
		deselectBodyPart($(target));
	});
}

function selectBodyPart($target){
	var part = $target.data('part');

	$target.css({
		'fill': 'lightgreen',
		'opacity': .9
	});

	bodyParts.push(part);
	$target.addClass('selected');
}

function deselectBodyPart($target){
	var part = $target.data('part'),
		idx;

	$target.css('fill', 'transparent');

	if((idx = bodyParts.indexOf(part)) !== -1){
		bodyParts.splice(idx, 1);
	}

	$target.removeClass('selected');
}

function bodyPart_Click(e){
	var $target = $(e.target);

	if($target.hasClass('selected')){
		deselectBodyPart($target);
	}
	else{
		selectBodyPart($target);
	}
}
</script>
</body>
</html>