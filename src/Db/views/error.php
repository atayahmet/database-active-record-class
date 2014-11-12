<style type="text/css">
	.dbExCon  {margin:auto;width:98%;height:auto;background-color:#fc8b35;padding:10px;border-left:5px solid #ffae00;}
	.dbExCon h1 {color:#fff;font-size:22;height:25px;font-style:italic;}
	.dbExMsg {color:#fff;margin-left:20px;}
	.dbExInnerBg {background-color:#000;padding:10px;border-left:solid 4px green;}
	.yellow {color:yellow;}
	.red {color:#f93b3b;}
	.area10 {width:1px;height:10px;}
</style>

<div class="dbExCon">
	<h1>Database error</h1>
	<div class="dbExInnerBg">
		<div class="dbExMsg red"><strong>Message:</strong> <?php echo $ex['e']->getMessage();?></div>
		<div class="area10"></div>
		<?php if(isset($ex['q'])):?>
		<div class="dbExMsg yellow"><strong>Query:</strong> <?php echo $ex['q'];?></div>
		<?php endif;?>
		
		<?php if(isset($ex['p']) && !is_null($ex['p'])):?>
		<div class="dbExMsg yellow"><strong>Parameter:</strong> <?php echo $ex['p'];?></div>
		<?php endif;?>
	</div>
</div>
