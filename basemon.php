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
for ($i=0; $i<$nr_rigs; $i++)
{
	$pool_active = '';
	if (SHOW_POOLS)
	{
		$pool_priority = 999;
		foreach ($r[$i]['pools'] as $key=>$pool)
		{
			if ($key != 'STATUS')
			{
				if (($pool['Status'] == 'Alive') && ($pool['Priority'] < $pool_priority))
				{
					$pool_priority = $pool['Priority'];
					$pool_active = '<br><span style="font-weight:bold">Pool ' . $pool['POOL'] . ' - ' . $pool['URL'] . '</span>';
				}
			}
		}
	}
	?>
<br>
<center>
	<div class="CSSTableGenerator" >
	<table>
		<tr>
			<td colspan=9><font size=4><?php echo $r[$i]['name']?></font></td>
		</tr>
		<tr>
			<td><b>Device</b></td>
			<td><b>Status</b></td>
			<td><b>Temp</b></td>
			<td><b>Fan</b></td>
			<td><b><?php echo $r[$i]['coin']['COIN']['Hash Method'] == 'scrypt' ? 'KH/s' : 'MH/s'?> (5s | avg)</b></td>
			<td><b>A</b></td>
			<td><b>R</b></td>
			<td><b>HW</b></td>
			<td><b>Invalid</b></td>
			
		</tr>
		<?php
		if (isset ($r[$i]['devs']))
		{
			foreach ($r[$i]['devs'] as $key=>$dev)
			{
				if ($key != 'STATUS')
				{
					$invalid_ratio = 0;
					$total_shares =  $dev['Accepted'] + $dev['Rejected'];
					if ($total_shares > 0)
					{
						$invalid_ratio = round(($dev['Rejected'] / $total_shares) * 100,2);
					}
					?>
					<tr>
						<td>
							<?php
							if (isset ($dev['GPU']))
							{
								echo 'GPU ' . $dev['GPU'];
							}
							else if (isset ($dev['ASC']))
							{
								echo 'ASC ' . $dev['ASC'];
							}
							else if (isset ($dev['PGA']))
							{
								echo 'PGA ' . $dev['PGA'];
							}
							?>
						</td>
						<td><?php echo $dev['Status'] == 'Alive' ? '<span class="ok">' . $dev['Status'] . '</span>' : '<span class="error">' . $dev['Status'] . '</span>' ?></td>
						<td><?php echo $dev['Temperature'] > ALERT_TEMP ? '<span class="error">' . round($dev['Temperature']) . 'Â°C</span>' : round($dev['Temperature']) . 'Â°C' ?></td>
						<td><?php echo $dev['Fan Percent']?>%</td>
						<td style="text-align:center">
							<?php
							$stats_second = isset ($dev['MHS 5s']) ? $dev['MHS 5s'] : (isset ($dev['MHS 2s']) ? $dev['MHS 2s'] : 0);
							$stats_second_string = $r[$i]['coin']['COIN']['Hash Method'] == 'scrypt' ? $stats_second * 1000 . ' | ' . $dev['MHS av'] * 1000 : $stats_second . ' | ' . $dev['MHS av'];
							$stats_ratio = 0;
							if ($dev['MHS av'] > 0)
							{
								$stats_ratio = $stats_second / $dev['MHS av'];
							}

							if (100 - ($stats_ratio * 100) >= ALERT_MHS)
							{
								echo '<span class="error">' . $stats_second_string . '</span>';
							}
							else
							{
								echo $stats_second_string;
							}
							?>
						</td>
						<td><?php echo $dev['Accepted']?></td>
						<td><?php echo $dev['Rejected']?></td>
						<td><?php echo $dev['Hardware Errors'] == 0  ? '<span class="ok">0</span>' : '<span class="error">' . $dev['Hardware Errors'] . '</span>' ?></td>
						<td><?php echo $invalid_ratio <= ALERT_STALES  ? $invalid_ratio . '%' : '<span class="error">' . $invalid_ratio . '%</span>' ?></td>
					</tr>
					<tr>
						<td colspan=9><center><font size=2 color=green><?php echo $pool_active?></center></td>
					</tr>

					<?php
				}
			}
		}
		else
		{
			?>
			<tr>
				<td colspan="10" style="text-align:center" class="error">OFFLINE</td>
			</tr>
			<?php
		}
		?>
	</table>
	</div>

	<?php
}
?>


