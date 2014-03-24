<?php

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

	<div class="CSSTableGenerator" >
	<table>
        <tr>
                <td>Miner</td>
                <td>Status</td>
                <td>Uptime</td>
                <td>MH/s</td>
                <td>A</td>
                <td>R</td>
                <td>Invalid</td>
                <td>WU</td>
                <td>WU ratio</td>
        </tr>

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

		<tr>
			<td><?php echo $r[$i]['name']?></td>
			<td><?php echo $r[$i]['summary']['STATUS']['STATUS'] == 'S' ? '<span class="ok">ONLINE</span>' : '<span class="error">OFFLINE</span>' ?></td>
			<td><?php echo $running?></td>
			<td><?php echo $r[$i]['summary']['SUMMARY']['MHS av']?></td>
			<td><?php echo $r[$i]['summary']['SUMMARY']['Accepted']?></td>
			<td><?php echo $r[$i]['summary']['SUMMARY']['Rejected']?></td>
			<td><?php echo $invalid_ratio <= ALERT_STALES  ? $invalid_ratio . '%' : '<span class="error">' . $invalid_ratio . '%</span>' ?></td>
			<td><?php echo $r[$i]['summary']['SUMMARY']['Work Utility']?></td>
			<td><?php echo $wu_ratio?></td>
		</tr>
		<?php
	}

	if ($a_sum > 0)
	{
		$invalid_sum_ratio = round(($r_sum / $a_sum) * 100, 2);
	}

	?>
	</table>
</div>



