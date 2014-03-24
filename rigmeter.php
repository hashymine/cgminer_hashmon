<?php
/*########################################################
HashMon CGMINER REMOTE MONITORING SCRIPT WITH ALERTS
Hackedp By: hashymine
Version: 2.420

If you like it please support it with donating:
BTC : 1FEsSfkAwRy6X6CN52R3Pf2NqKGKpypDCF
########################################################*/

include_once ('./functions.inc.php');

$nr_rigs = count($r);

for ($i=0; $i<$nr_rigs; $i++)
{
	$r[$i]['summary'] = request('summary', $r[$i]['ip'], $r[$i]['port']);
	if ($r[$i]['summary'] != null)
	{
		$r[$i]['devs']  = request('devs',  $r[$i]['ip'], $r[$i]['port']);
		$r[$i]['stats'] = request('stats', $r[$i]['ip'], $r[$i]['port']);
		$r[$i]['pools'] = SHOW_POOLS ? request('pools', $r[$i]['ip'], $r[$i]['port']) : FALSE;
		$r[$i]['coin']  = request('coin',  $r[$i]['ip'], $r[$i]['port']);
	}
}
?>


	<?php
	$hash_sum          = 0;
	$a_sum             = 0;
	$r_sum             = 0;
	$hw_sum            = 0;
	$wu_sum            = 0;
	$invalid_sum_ratio = 0;

	for ($i=0; $i<$nr_rigs; $i++)
	{
		$r[$i]['summary']['STATUS']['STATUS']           = isset($r[$i]['summary']['STATUS']['STATUS'])           ? $r[$i]['summary']['STATUS']['STATUS']           : 'OFFLINE';
		$r[$i]['summary']['SUMMARY']['MHS av']          = isset($r[$i]['summary']['SUMMARY']['MHS av'])          ? $r[$i]['summary']['SUMMARY']['MHS av']          : 0;
		$r[$i]['summary']['SUMMARY']['Accepted']        = isset($r[$i]['summary']['SUMMARY']['Accepted'])        ? $r[$i]['summary']['SUMMARY']['Accepted']        : 0;
		$r[$i]['summary']['SUMMARY']['Rejected']        = isset($r[$i]['summary']['SUMMARY']['Rejected'])        ? $r[$i]['summary']['SUMMARY']['Rejected']        : 0;
		$r[$i]['summary']['SUMMARY']['Hardware Errors'] = isset($r[$i]['summary']['SUMMARY']['Hardware Errors']) ? $r[$i]['summary']['SUMMARY']['Hardware Errors'] : 0;
		$r[$i]['summary']['SUMMARY']['Work Utility']    = isset($r[$i]['summary']['SUMMARY']['Work Utility'])    ? $r[$i]['summary']['SUMMARY']['Work Utility']    : 0;
		$r[$i]['stats']['STATS0']['Elapsed']            = isset($r[$i]['stats']['STATS0']['Elapsed'])            ? $r[$i]['stats']['STATS0']['Elapsed']            : 'N/A';
		$r[$i]['coin']['COIN']['Hash Method']           = isset($r[$i]['coin']['COIN']['Hash Method'])           ? $r[$i]['coin']['COIN']['Hash Method']           : 'sha256';

		$invalid_ratio = 0;
		$wu_ratio      = 0;

		if (($r[$i]['summary']['SUMMARY']['Accepted'] + $r[$i]['summary']['SUMMARY']['Rejected']) > 0)
		{
			$invalid_ratio = round(($r[$i]['summary']['SUMMARY']['Rejected'] / ($r[$i]['summary']['SUMMARY']['Accepted'] + $r[$i]['summary']['SUMMARY']['Rejected'])) * 100,2);
		}

		if ($r[$i]['stats']['STATS0']['Elapsed'] == 'N/A')
		{
			$running = 'N/A';
		}
		else
		{
			$t = seconds_to_time($r[$i]['stats']['STATS0']['Elapsed']);
			$running = $t['d'] . 'd ' . $t['h'] . ':' . $t['m'] . ':' . $t['s'];
		}

		if ($r[$i]['summary']['SUMMARY']['MHS av'] > 0)
		{
			$wu_ratio = round($r[$i]['summary']['SUMMARY']['Work Utility'] / ($r[$i]['summary']['SUMMARY']['MHS av']*1000),3);
			if ($wu_ratio < 0.9 && $t['d']>=1)
			{
				$wu_ratio = '<span class="error">' . $wu_ratio . '</span>';
			}
		}
		
		$hash_sum = $hash_sum + $r[$i]['summary']['SUMMARY']['MHS av'];
		$a_sum    = $a_sum    + $r[$i]['summary']['SUMMARY']['Accepted'];
		$r_sum    = $r_sum    + $r[$i]['summary']['SUMMARY']['Rejected'];
		$hw_sum   = $hw_sum   + $r[$i]['summary']['SUMMARY']['Hardware Errors'];
		$wu_sum   = $wu_sum   + $r[$i]['summary']['SUMMARY']['Work Utility'];

		?>

		<?php
	}

	if ($a_sum > 0)
	{
		$invalid_sum_ratio = round(($r_sum / $a_sum) * 100, 2);
	}

	?>

    <link rel="stylesheet" type="text/css" href="theme.css">
    <link class="include" rel="stylesheet" type="text/css" href="includes/dist/jquery.jqplot.min.css" />
    <link type="text/css" rel="stylesheet" href="includes/dist/examples/syntaxhighlighter/styles/shCoreDefault.min.css" />
    <!--[if lt IE 9]><script language="javascript" type="text/javascript" src="includes/dist/excanvas.js"></script><![endif]-->
    <script class="include" type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script type="text/javascript">
    hashsum = [<?php echo $hash_sum?>];

$(document).ready(function(){

   plot3 = $.jqplot('chart3', [hashsum],{
        title: 'Hashys Total',
        seriesDefaults: {
           renderer: $.jqplot.MeterGaugeRenderer,
           rendererOptions: {
                max: 5,
		label: '<?php echo $hash_sum?> MH/s',
		labelPosition: 'bottom',
		labelHeightAdjust: -5
           }
       }
   });
});

    asum = [<?php echo $a_sum?>];

$(document).ready(function(){

   plot5 = $.jqplot('chart5', [asum],{
        title: 'Accepted',
        seriesDefaults: {
           renderer: $.jqplot.MeterGaugeRenderer,
           rendererOptions: {
                label: '<?php echo $a_sum?> Shares',
		labelPosition: 'bottom',
		labelHeightAdjust: -5
           }
       }
   });
});

    rsum = [<?php echo $r_sum?>];

$(document).ready(function(){

   plot6 = $.jqplot('chart6', [rsum],{
        title: 'Rejected',
        seriesDefaults: {
           renderer: $.jqplot.MeterGaugeRenderer,
           rendererOptions: {
		label: '<?php echo $r_sum?> Shares',
		labelPosition: 'bottom',
		max: 5000,
		labelHeightAdjust: -5
           }
       }
   });
});

    rper = [<?php echo $invalid_sum_ratio?>];

$(document).ready(function(){

   plot7 = $.jqplot('chart7', [rper],{
        title: 'Ratio',
        seriesDefaults: {
           renderer: $.jqplot.MeterGaugeRenderer,
           rendererOptions: {
                label: '<?php echo $invalid_sum_ratio?> %',
		labelPosition: 'bottom',
		max: 100,
		showTickLabels: false,
		intervals:[10,35,100],
		intervalColors:['#66cc66', '#E7E658', '#cc6666'],
		labelHeightAdjust: -5
           }
       }
   });
});

    wut = [<?php echo $wu_sum?>];

$(document).ready(function(){

   plot8 = $.jqplot('chart8', [wut],{
        title: 'WU',
        seriesDefaults: {
           renderer: $.jqplot.MeterGaugeRenderer,
           rendererOptions: {
                label: '<?php echo $wu_sum?>',
                labelPosition: 'bottom',
		labelHeightAdjust: -5
           }
       }
   });
});
</script>

    <script class="include" type="text/javascript" src="includes/dist/jquery.jqplot.min.js"></script>
    <script type="text/javascript" src="includes/dist/examples/syntaxhighlighter/scripts/shCore.min.js"></script>
    <script type="text/javascript" src="includes/dist/examples/syntaxhighlighter/scripts/shBrushJScript.min.js"></script>
    <script type="text/javascript" src="includes/dist/examples/syntaxhighlighter/scripts/shBrushXml.min.js"></script>
    <script class="include" type="text/javascript" src="includes/dist/plugins/jqplot.meterGaugeRenderer.min.js"></script>

    <table>
	<tr>
        	<td><div id="chart3" class="plot" style="width:260px;height:180px;"></div></td>
                <td><div id="chart5" class="plot" style="width:260px;height:180px;"></div></td>
                <td><div id="chart6" class="plot" style="width:260px;height:180px;"></div></td>
                <td><div id="chart7" class="plot" style="width:260px;height:180px;"></div></td>
                <td><div id="chart8" class="plot" style="width:260px;height:180px;"></div></td>
        </tr>
    </table>

