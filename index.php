<?php
	ob_start();

	$temp = shell_exec('cat /sys/class/thermal/thermal_zone*/temp');
	$temp = round($temp / 1000, 1);

	$clock = shell_exec('cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq');
	$clock = round($clock / 1000);

	$voltage = shell_exec('/opt/vc/bin/vcgencmd measure_volts');
	$voltage = explode("=", $voltage);
	$voltage = $voltage[1];
	$voltage = substr($voltage, 0, -2);

	$cpuusage = 100 - shell_exec("vmstat | tail -1 | awk '{print $15}'");

        $datetime = shell_exec('date');

	$uptimedata = shell_exec('uptime');
	$uptime = explode(' up ', $uptimedata);
	$uptime = explode(', ', $uptime[1]);
        $uptimehm = explode(':', $uptime[1]);
	$hs = '';
        if ($uptimehm[0] > 1)
		$hs = 's';
	$ms = '';
	if ($uptimehm[1] > 1)
                $ms = 's';
	$uptime = $uptime[0] . $uptimehm[0] . ' hour' . $hs . ' ' . $uptimehm[1] . ' minute' . $ms;
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Raspberry Pi Control Panel</title>
	<link rel="stylesheet" href="stylesheets/main.css">
	<script src="javascript/raphael.2.1.0.min.js"></script>
	<script src="javascript/justgage.1.0.1.min.js"></script>

	<script>
		function checkAction(action) {
			if (confirm('<?php echo TXT_CONFIRM; ?> ' + action + '?')) {
				return true;
			} else {
				return false;
			}
		}

		window.onload = doLoad;

		function doLoad() {
			setTimeout("refresh()", 10 * 1000);
		}

		function refresh() {
			window.location.reload(false);
		}
	</script>
</head>

<body>
	<div id="container">
		<img id="logo" src="images/logo.png">
		<div id="title">Raspberry Pi PHP Info Panel</div>
		<div id="uptime">
			<br/>
			<b>Datetime:</b>
			&nbsp;&nbsp;
			<?php echo $datetime; ?>
			<span STYLE="font-size: 8px;"></span>
		</div>

		<?php if (isset($uptime)) { ?>
                        <div id="uptime">
                                <br/>
                                <b>Uptime:</b>
                                &nbsp;&nbsp;
                                <?php echo $uptime; ?>
                                <span STYLE="font-size: 8px;"></span>
                        </div>
                <?php } ?>


		<?php if (isset($temp) && is_numeric($temp)) { ?>
			<div id="tempgauge"></div>
			<script>
				var t = new JustGage({
					id: "tempgauge",
					value: <?php echo $temp; ?>,
					min: 0,
					max: 100,
					title: "Temperature",
					label: "°C"
				});
			</script>
		<?php } ?>

		<?php if (isset($voltage) && is_numeric($voltage)) { ?>
			<div id="voltgauge"></div>
			<script>
				var v = new JustGage({
					id: "voltgauge",
					value: <?php echo $voltage; ?>,
					min: 0.8,
					max: 1.4,
					title: "CPU Voltage",
					label: "V"
				});
			</script>
		<?php } ?>

		<?php if (isset($cpuusage) && is_numeric($cpuusage)) { ?>
			<div id="cpugauge"></div>
			<script>
				var u = new JustGage({
					id: "cpugauge",
					value: <?php echo $cpuusage; ?>,
					min: 0,
					max: 100,
					title: "CPU Usage",
					label: "%"
				});
			</script>
		<?php } ?>

		<?php if (isset($clock) && is_numeric($clock)) { ?>
			<div id="clockgauge"></div>
			<script>
				var c = new JustGage({
					id: "clockgauge",
					value: <?php echo $clock; ?>,
					min: 0,
					max: 1000,
					title: "CPU Clock Speed",
					label: "MHz"
				});
			</script>
		<?php } ?>
	</div>
</body>

</html>
