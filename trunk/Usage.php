<?php
	//include_once("Include\\Settings.php");
	include_once("Include/SettingsWebApp.php");
	include_once('Include/SettingsBranding.php');
	include_once("Include/SettingsDatabase.php");
	include_once("Include/HtmlCommon.php");
	include_once('Include/ObjectUsage.php');
	
	$watcher 		= new UsageObject();
	$look_at_user 	= False;
	
	if(isset($_GET["q"])){
		$look_at_user = $_GET["q"];
		if($watcher->lookupIpAddress($look_at_user) == False){
			$look_at_user = False;
		}
	}
	
	echo commonHtmlPageHead("Usage");
	echo commonHtmlPlainHeader();
	
	
	$count_ips				= $watcher->countIpAddresses();
	$count_search			= $watcher->countSearches();
	$count_bound_search 	= $watcher->countBoundSearches();
	$count_bound_objects 	= $watcher->countBoundObjects();
	$count_objects 			= $watcher->countObjects();
	$count_relationships 	= $watcher->countRelationships();
	$count_attributes		= $watcher->countAttributes();
	$recent_searches 		= $watcher->recentSearches();
	$recent_objects			= $watcher->recentNavigated();
	$recent_users			= $watcher->recentUsers();
	
	if($look_at_user == False){
		$current_user 			= $_SERVER["REMOTE_ADDR"];
	} else{
		$current_user 			= $look_at_user;
	}
	$host_name				= gethostbyaddr($current_user);
	$profile_id				= $watcher->lookupIpAddress($current_user);
	$recent_user_searches   = $watcher->recentUserSearches($current_user);
	$recent_user_objects 	= $watcher->recentUserNavigated($current_user);
?>
<br />
<div class="about_section_heading">Privacy Statement</div>
<div class="about_section_content">
<br >
This application tracks your usage via your IP address. You're IP address is captured via the HTTP
headers sent to the application when you request a page. This information is captured in order to 
better understand how users are interacting with the tool. There in no personal information other 
then your IP address being tracked. This page displays some of your recent interaction with the tool, 
as well as some other recent user actions. 
<br /><br />
</div>
<div class="about_section_heading">User Profile</div>
<div class="about_section_content">
<br />
<center>
	<table class="usage_table">
		<?php
			if($current_user != $_SERVER["REMOTE_ADDR"]){
				echo "<tr><td class=\"usage_value_name\">Your IP Address</td>";
				echo "<td class=\"usage_value_value\">";
				echo "<a href=\"Usage.php?q=".urlencode($_SERVER["REMOTE_ADDR"])."\">".$_SERVER["REMOTE_ADDR"]."</td></tr>";
			}
		?>
		<tr><td class="usage_value_name">Profile IP Address</td>
		<td class="usage_value_value">
		<?php echo $current_user; ?></td></tr>
		<tr><td class="usage_value_name">Profile Host Name</td>
		<td class="usage_value_value">
		<?php echo $host_name; ?></td></tr>
		<tr><td class="usage_value_name">Profile ID</td>
		<td class="usage_value_value">
		<?php echo $profile_id; ?></td></tr>
	</table>
</center>

<br />
<center>
	<table width="99%">
	<tr><td width="50%" style="vertical-align:top;">
	<center>
		<table width="99%">
		<tr><th class="usage_value_name">Searches by Profile</th></tr>
		<?php
			if(count($recent_user_searches) > 0){
				foreach($recent_user_searches as $index=>$text){
					$pos = $index + 1;
					echo "<tr><td class=\"usage_value_name\">";
					echo "<a href=\"Search.php?q=". urlencode($text)."\">";
					echo substr($text,0,40);
					if(strlen($text) > 40) echo "...";
					echo "</a>";
					echo "</td></tr>\n";
				}
			} else {
				echo "<tr><td class=\"usage_value_name\">None</td></tr>";
			}
		?>
		</table>
	</center>
	</td><td width="50%" style="vertical-align:top;">
	<center>
		<table width="99%">
		<tr><th class="usage_value_name">Objects Visited by Profile</th></tr>
		<?php
			foreach($recent_user_objects as $index=>$text){
				$pos = $index + 1;
				echo "<tr><td class=\"usage_value_name\">";
				echo "<a href=\"Navigate?q=". urlencode($text)."\">";
				echo substr($text,0,40);
				if(strlen($text) > 40) echo "...";
				echo "</a>";
				echo "</td></tr>\n";
			}
		?>
		</table>
	</center>
	</td></tr>
	</table>
</center>
<br /><br />
</div>
<div class="about_section_heading">Rati Database Counts</div>
<div class="about_section_content">
<br />
<center>
	<table class="usage_table">
		<tr><td class="usage_value_name">Total Number of Unique Users</td>
		<td class="usage_value_value">
		<?php echo $count_ips; ?>
		</td></tr>
		<tr><td class="usage_value_name">Total Number of Unique Searches</td>
		<td class="usage_value_value">
		<?php echo $count_search; ?>
		</td></tr>
		<tr><td class="usage_value_name">Total Number of Seaches Bound to Users</td>
		<td class="usage_value_value">
		<?php echo $count_bound_search; ?>
		</td></tr>
		<tr><td class="usage_value_name">Total Number of Objects Bound to Users</td>
		<td class="usage_value_value">
		<?php echo $count_bound_objects; ?>
		</td></tr>
		<tr><td class="usage_value_name">Total Number of Objects</td>
		<td class="usage_value_value">
		<?php echo $count_objects; ?>
		</td></tr>
		<tr><td class="usage_value_name">Total Number of Attributes</td>
		<td class="usage_value_value">
		<?php echo $count_attributes; ?>
		</td></tr>
		<tr><td class="usage_value_name">Total Number of Relationships between Objects and Attributes</td>
		<td class="usage_value_value">
		<?php echo $count_relationships; ?>
		</td></tr>
	</table>
</center>
<br /><br />
</div>

<div class="about_section_heading">Recent Global Rati Actions</div>
<div class="about_section_content">
<br />
<center>
	<table width="99%">
	<tr><td width="33%" style="vertical-align:top;">
	<center>
		<table width="99%">
		<tr><th class="usage_value_name">New Unique Searches</th></tr>
		<?php
			foreach($recent_searches as $index=>$text){
				//$pos = $index + 1;
				echo "<tr><td class=\"usage_value_name\">";
				echo "<a href=\"Search.php?q=". urlencode($text)."\">";
				echo substr($text,0,30);
				if(strlen($text) > 30) echo "...";
				//$text</a>";
				echo "</td></tr>\n";
			}
		?>
		</table>
	</center>
	</td><td width="33%" style="vertical-align:top;">
	<center>
		<table width="99%">
		<tr><th class="usage_value_name">New Recent Object Visits</th></tr>
		<?php
			foreach($recent_objects	as $index=>$text){
				//$pos = $index + 1;
				echo "<tr><td class=\"usage_value_name\"><a href=\"Navigate.php?q=". urlencode($text)."\">";
				echo substr($text,0,30);
				if(strlen($text) > 30) echo "...";
				echo "</a>";
				echo "</td></tr>\n";
			}
		?>
		</table>
	</center>
	</td><td width="33%" style="vertical-align:top;">
	<center>
		<table width="99%">
		<tr><th class="usage_value_name">New Recent Users</th></tr>
		<?php
			foreach($recent_users	as $index=>$text){
				//$pos = $index + 1;
				echo "<tr><td class=\"usage_value_name\">";
				echo "<a href=\"Usage.php?q=".urlencode($text)."\">$text</a></td></tr>\n";
			}
		?>
		</table>
	</center></tr>
	</table>
</center>
<br />
</div>

<?php echo commonHtmlPageFooter(); ?>