<?php 
// edit: wp-content/plugins/events-manager/classes/em-events.php

// (around line 163; method name 'output')
// --- start changes

// >>> find:
$events = apply_filters('em_events_output_events', $events);

// >>> add (after):
$months = array();
$years = array();


// >>> find
foreach ( $events as $EM_Event ) {

// >>> add (after):
if(isset($_REQUEST['calendar_day']) && isset($EM_Event->post_id)) {
	$date=explode("-",$_REQUEST['calendar_day']);
	$year=$date[1];
	$month=$date[0];
	$event_date = explode("-",$EM_Event->event_start_date);
					
					if(!array_search($event_date[1],$months[$event_date[0]])) {
		$months[$event_date[0]][]=$event_date[1];
		$years[]=$event_date[0];
	}
					
	if($event_date[0] != $year || $event_date[1] != $month)
		continue;
}

// >>> find
// TODO check if reference is ok when restoring object, due to changes in php5 v 4
		$EM_Event = $EM_Event_old;
		$output = apply_filters('em_events_output', $output, $events, $args);

// >>> add after
if(isset($_REQUEST['calendar_day'])) {
	$date=explode("-",$_REQUEST['calendar_day']);
	$year=$date[1];
	$month=$date[0];
	
	$html='<div class="em-calendar-wrapper"><table style="width:100%;margin-bottom:20px;"><thead><tr>';
	
	$next=0;
	$prev=0;
	foreach($months[$year] as $m) {
		if($m>$month && (!$next || $m<$next)) {
			$next="$m-$year";
		}
		if($m<$month && $m>$prev) {
			$prev="$m-$year";
		}
	}
	if($prev==0 || $next==0) {
		foreach($years as $y) {
			if($y>$year && !$next) {
				$next=$months[$y][0]."-$y";
			}
			if($y<$year && !$prev) {
				$prev=end($months[$y])."-$y";
			}
		}
	}
	
	$html.='<td width="20%;">';
	
	if($prev)
		$html.='<a href="../'.$prev.'/">&lt;&lt;</a>';
		
	$html.='</td>';
	$html.='<td width="60%;" class="month_name" colspan="5">'.date("F", mktime(0, 0, 0, $month, 10)).' '. $year . '</td>';
	$html.='<td width="20%;">';
	
	if($next)
		$html.='<a href="../'.$next.'/">&gt;&gt;</a>';
		
	$html.='</td>';
	
	$html.='</tr></thead></table></div>';
	
	$output=$html.$output;
}

// --- end of changes
?>
