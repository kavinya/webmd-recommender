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
		position: relative;
		display: none;
		opacity: 0;
	}
	
	.list {
		max-height: 60vh;
		overflow: auto;
	}
	
	.back-btn {
		position: absolute;
		top: .5em;
		left: .5em;
	}
	
	.area{
		background-color: pink;
		position: absolute;
	}
	
	.body-part{
		z-index: 10;
		cursor: pointer;
	}
	
	.topic {
		cursor: pointer;
	}
	
	.topic:hover, .topic.selected,
	.question:hover, .question.selected{
		background-color: lightblue;
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
			<div class="jumbotron">
				<h1>Welcome to our Webmd Recommender Thing</h1>
				<p>Where we help you, help you.</p>
				<button data-from-id='#title' data-to-id='#sex' class='transition btn btn-primary btn-lg btn-block'>Get Started</button>
			</div>
		</div>
	</div>
	<div id='sex' class='fullscreen'>
		<button data-from-id='#sex' data-to-id='#title' class='back-btn transition btn btn-warning'>Back</button>
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
		<button data-from-id='#topic' data-to-id='#sex' class='back-btn transition btn btn-warning'>Back</button>
		<div class='row vcenter'>
			<h1>What area(s) are you interested in?</h1>
			<div class='sex-col col-lg-6 col-md-6 col-sm-6 col-xs-12'>
				<div id='male-body' class='human-body' style='display: none;'>
					<?php echo $maleSVG; ?>
				</div>
				<div id='female-body' class='human-body'>
					<img class='sex-img' data-sex='female' src='img/aiga-toilet-women.png'>
				</div>
				<div class='row'>
					<div class='col-lg-offset-3 col-lg-6, col-md-offset-2 col-md-8 col-sm-offset-2 col-sm-8 col-xs-offset-2 col-xs-8'>
						<button style='display: none;' id='send-body-parts-btn' class='btn btn-primary btn-lg btn-block'>Discover Related Topics</button>
					</div>
				</div>
			</div>
			<div class='sex-col col-lg-6 col-md-6 col-sm-6 col-xs-12'>
				<h2>Related Topics</h2>
				<div id='topic-list' class='list'></div>
				<div class='row'>
					<div class='col-lg-offset-3 col-lg-6, col-md-offset-2 col-md-8 col-sm-offset-2 col-sm-8 col-xs-offset-2 col-xs-8'>
						<button data-from-id='#topic' data-to-id='#question' style='display: none;' id='send-topics-btn' class='btn btn-primary btn-lg btn-block'>Get Related Questions</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id='question' class='fullscreen'>
		<button data-from-id='#question' data-to-id='#topic' class='back-btn transition btn btn-warning'>Back</button>
		<div class='row vcenter'>
			<h1>Resolve Your Problem(s)</h1>
			<div class='sex-col col-lg-6 col-md-6 col-sm-6 col-xs-12'>
				<h2>Related Questions</h2>
				<div id='question-list' class='list'></div>
				<button style='display: none;' id='send-questions-btn' class='btn btn-primary form-control'>Get Potential Answers</button>
			</div>
			<div class='sex-col col-lg-6 col-md-6 col-sm-6 col-xs-12'>
				<h2>Potential Answers</h2>
				<div id='answer-list' class='list'></div>
			</div>
		</div>
	</div>
</div>
<script src='lib/jquery/dist/jquery.min.js'></script>
<script src='lib/bootstrap/dist/js/bootstrap.min.js'></script>
<script>
var bodyParts = [],
	servers = {};

//Connect to servers
generateIndexServer();
generateTopicServer();
generateQuestionServer();

function generateIndexServer(){
	servers.index = new WebSocket("ws://localhost:8765/");

	//Define index server interactions
	servers.index.onmessage = function (event) {
	    var topics = JSON.parse(event.data),
	    	$list = $('#topic-list').empty();

	    $(topics).each(function(idx, topic){
	        var name = topic.topicname,
	        	id = topic.topicid,
	        	$topic = $(document.createElement('div')),
	        	$header = $(document.createElement('h4'));

	        $header
	        	.css('pointer-events', 'none')
	        	.append(name);

        	$topic
        		.addClass('topic')
        		.data('id', id)
        		.append($header)
        		.click(topic_Click);

	        $list.append($topic);
	    });

		vcenter($($list.closest('.vcenter')));
	};

	servers.index.onclose = function (event) {
		console.log('Re-establishing index server connection.');
		generateIndexServer();
	};

	servers.index.onerror = function (event) {
		console.error("Error in index server:" + event);
	};

	servers.index.onopen = function (event) {
		console.log('Established index server connection.');
	};
}

function generateTopicServer(){
	servers.topic = new WebSocket("ws://localhost:8766/");

	//Define topic server interactions
	servers.topic.onmessage = function (event) {
	    var questions = JSON.parse(event.data),
	    	$list = $('#question-list').empty();

	    $(questions).each(function(idx, question){
	        var title = question.questiontitle,
	        	topicIds = question.questiontopicid.split(', '),
	        	url = question.questionurl,
	        	date = question.questionpostdate,
	        	memberId = question.questionmemberid,
	        	questionId = question.questionid,
	        	$question = $(document.createElement('div')),
	        	$header = $(document.createElement('h4')),
	        	$link = $(document.createElement('a'));


        	$link
        		.html(title)
        		.attr('href', url);

	        $header
	        	.css('pointer-events', 'none')
	        	.append($link);

        	$question
        		.addClass('question')
        		.data('id', questionId)
        		.append($header)
        		.click(question_Click);

	        $list.append($question);
	    });

		swapSections($('#topic'), $('#question'));
	};

	servers.topic.onclose = function (event) {
		console.log('Re-establishing topic server connection.');
		generateTopicServer();
	};

	servers.topic.onerror = function (event) {
		console.error("Error in topic server:" + event);
	};

	servers.topic.onopen = function (event) {
		console.log('Established topic server connection.');
	};
}

function generateQuestionServer(){
	servers.question = new WebSocket("ws://localhost:8767/");

	//Define question server interactions
	servers.question.onmessage = function (event) {
		debugger;
	    var answers = JSON.parse(event.data),
	    	$list = $('#answer-list').empty();

	    $(answers).each(function(idx, answer){
	        var content = answer.answercontent,
	        	helpfulNum = answer.answerhelpfulnum,
	        	id = answer.answerid,
	        	memberId = answer.answermemberid,
	        	postDate = answer.answerpostdate,
	        	url = answer.answerquestionurl,
	        	voteNum = answer.answervotenum,
	        	questionId = answer.answerquestionid,
	        	$answer = $(document.createElement('div')),
	        	$header = $(document.createElement('h4'));


	        $header
	        	.css('pointer-events', 'none')
	        	.append(content);

        	$answer
        		.addClass('answer')
        		.data('id', id)
        		.append($header)
        		.click(answer_Click);

	        $list.append($answer);
	    });

		vcenter($($list.closest('.vcenter')));
	};

	servers.question.onclose = function (event) {
		console.log('Re-establishing question server connection.');
		generateQuestionServer();
	};

	servers.question.onerror = function (event) {
		console.error("Error in question server:" + event);
	};

	servers.question.onopen = function (event) {
		console.log('Established question server connection.');
	};
}

$(document).ready(function(){
	$('.sex-img').click(sexImg_Click);
	$('.transition').click(transition_Click);
	$('.body-part').click(bodyPart_Click);

	$('#send-body-parts-btn').click(sendBodyPartsButton_Click);
	$('#send-topics-btn').click(sendTopicsButton_Click);
	$('#send-questions-btn').click(sendQuestionsButton_Click);

	showSection($('#title'));
});

function transition_Click(e){
	var $target = $(event.target),
		fromId = $target.data('from-id'),
		toId = $target.data('to-id');

	swapSections($(fromId), $(toId));
}

function swapSections($from, $to){
	hideSection($from);	
	showSection($to);
}

function showSection($section){
	var $child = $($section.children('.row.vcenter'));

	$section.css('opacity', 0);
	$section.show();
	vcenter($child);
	$section.animate({'opacity': 1}, 2000);
}

function hideSection($section){
	$section.hide();
}

/*
 * Center elements vertically
 */

function vcenter($target){
	$target.css('padding-top', 0);

	var pHeight = $target.parent().height(),
		tHeight = $target.height(),
		margin = pHeight / 2 - tHeight / 2;

	$target.css({
		'padding-top': margin
	});
}

/*
 * Functions to trigger web socket communication 
 */

function sendBodyPartsButton_Click(e){
	var bodyParts = [];

	$('.body-part.selected').each(function(idx, obj){
		bodyParts.push($(obj).data('part'));
	});

	servers.index.send(bodyParts);
}

function sendTopicsButton_Click(e){
	var topics = [];

	$('.topic.selected').each(function(idx, obj){
		topics.push($(obj).data('id'));
	});

	servers.topic.send(topics);
}

function sendQuestionsButton_Click(e){
	var questions = [];

	$('.question.selected').each(function(idx, obj){
		questions.push($(obj).data('id'));
	});

	servers.question.send(questions);
}

/*
 * Functions for managin user interface with body parts in male/female svg 
 */

function bodyPart_Click(e){
	var $target = $(e.target),
		$btn = $('#send-body-parts-btn');

	if($target.hasClass('selected')){
		deselectBodyPart($target);

		if($('.body-part.selected').length == 0){
			$btn.hide();
		}
	}
	else{
		selectBodyPart($target);
		$btn.show();
	}
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

/*
 * Topic Selection
 */

function topic_Click(event){
	var $target = $(event.target),
		$sendTopicsBtn = $('#send-topics-btn');

	$target.toggleClass('selected');

	if($('.selected.topic').length > 0){
		$sendTopicsBtn.show();
	}
	else{
		$sendTopicsBtn.hide();
	}
}

/*
 * Question Selection
 */

function question_Click(event){
	var $target = $(event.target),
		$sendQuestionsBtn = $('#send-questions-btn');

	$target.toggleClass('selected');

	if($('.selected.question').length > 0){
		$sendQuestionsBtn.show();
	}
	else{
		$sendQuestionsBtn.hide();
	}
}

/*
 * Question Selection
 */

function answer_Click(event){
	
}

/*
 * Sex Selection
 */

function sexImg_Click(e){
	var $target = $(event.target),
		sex = $target.data('sex');
	
	$('.human-body').hide();
	$('#' + sex + '-body').show();

	topics = [];

	$('.body-part.selected').each(function(idx, target){
		deselectBodyPart($(target));
	});

	swapSections($('#sex'), $('#topic'));
}

</script>
</body>
</html>