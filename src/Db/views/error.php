<style type="text/css">
	.dbExCon  {margin:auto;width:98%;height:auto;background-color:#f26000;padding:10px;}
	.dbExCon h1 {color:#fff;font-size:22;height:25px;font-style:italic;}
	.dbExMsg {color:#fff;margin-left:30px;}
</style>

<div class="dbExCon">
	<h1>Database error</h1>
	<div class="dbExMsg"><strong>Message:</strong> <?php echo $ex['e']->getMessage();?></div>
	<br />
	<div class="dbExMsg"><strong>Query:</strong> sfsdfsdfsd</div>
</div>
