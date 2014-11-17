<style type="text/css">
    #dumpTable {width:100%;font-family:arial, helvetica, sans-serif;}
    #dumpTable tr td {padding:6px;font-size:12px;background-color: #edeca1;border:solid 1px #000;}
    #dumpTable .header {font-size:18px;font-weight:bold;}
</style>

<table id="dumpTable" border="0">

    <tr>
        <td><strong>Duration</strong></td>
        <td><strong>Query</strong></td>
    </tr>
    <?php $total = 0;?>
    <?php foreach($queries as $q):?>
        <?php $total += $q['duration'];?>
    <tr>
        <td><?php echo $q['duration'];?></td>
        <td><?php echo $q['query'];?></td>
    </tr>
    <?php endforeach;?>
    <thead>
    <tr>
        <td class="header" colspan="2">QUERY DUMP</td>
    </tr>
    <tr>
        <td colspan="2"><strong>Total Query:</strong> <?php echo count($queries);?></td>
    </tr>
    </thead>
</table>