<?php

/**
 * @file
 * Javascript source to print the total sites onto the page.
 *
 * Available variables:
 *
 * $aggregates - the value of the 'ossap_stats_aggregates' variable, an indexed
 * array where keys are the statistic's description, and values are numeric
 * values.
 */

$src = url('ossap/stats.js', array('absolute' => TRUE));

if (isset($os_version)) {
  $aggregates['os_version'] = $os_version;
}
if (isset($messages) && !empty($messages)) {
  $aggregates['activity-messages'] = $messages;
}
if (isset($getsatisfaction) && !empty($getsatisfaction)) {
  foreach ($getsatisfaction as $key => $value) {
    $aggregates["gs-{$key}"] = $value;
  }
}
if (isset($visited_sites_in_day) && !empty($visited_sites_in_day)) {
  foreach ($visited_sites_in_day as $key => $value) {
    $aggregates["most-visited-sites-in-day{$key}"] = "{$value[0]} {$value[1]}";
  }
}
if (isset($visited_sites_in_week) && !empty($visited_sites_in_week)) {
  foreach ($visited_sites_in_week as $key => $value) {
    $aggregates["most-visited-sites-in-week{$key}"] = "{$value[0]} {$value[1]}";
  }
}
if (isset($visited_sites_in_month) && !empty($visited_sites_in_month)) {
  foreach ($visited_sites_in_month as $key => $value) {
    $aggregates["most-visited-sites-in-month{$key}"] = "{$value[0]} {$value[1]}";
  }
}
if (isset($viewed_pages_in_day) && !empty($viewed_pages_in_day)) {
  foreach ($viewed_pages_in_day as $key => $value) {
    $aggregates["most-viewed-pages-in-day{$key}"] = "{$value[0]} {$value[1]}";
  }
}
if (isset($viewed_pages_in_week) && !empty($viewed_pages_in_week)) {
  foreach ($viewed_pages_in_week as $key => $value) {
    $aggregates["most-viewed-pages-in-week{$key}"] = "{$value[0]} {$value[1]}";
  }
}
if (isset($viewed_pages_in_month) && !empty($viewed_pages_in_month)) {
  foreach ($viewed_pages_in_month as $key => $value) {
    $aggregates["most-viewed-pages-in-month{$key}"] = "{$value[0]} {$value[1]}";
  }
}
if (isset($rt_visitors) && !empty($rt_visitors)) {
  $aggregates['real-time-visitors'] = $rt_visitors;
}

if (isset($rt_visitors_week) && !empty($rt_visitors_week)) {
  $aggregates['weekly-visitors'] = $rt_visitors_week;
}

if (isset($rt_visitors_month) && !empty($rt_visitors_month)) {
  $aggregates['monthly-visitors'] = $rt_visitors_month;
}

if (isset($rt_pageview_week) && !empty($rt_pageview_week)) {
  $aggregates['weekly-pv'] = $rt_pageview_week;
}

if (isset($rt_pageview_month) && !empty($rt_pageview_month)) {
  $aggregates['monthly-pv'] = $rt_pageview_month;
}

?>
/**
 * Include this JS file to embed aggregated OpenScholar SAP stats on any page.
 * Like this:
 * @code
 * <script type="text/javascript" src="<?php echo $src; ?>"></script>
 * @endcode
 *
<?php
if (empty($aggregates)) {
  echo " * Something is wrong. Either no child domains are configured, or the\n";
  echo " * ossap_stats cron job needs to be run. Contact a system administrator.\n";
  echo " */";
  die();
}
?>
 * Your HTML markup may use any of the following id attributes to get numbers:
 * @code
<? foreach ($aggregates as $key => $value): ?>
 * <span class="ossap-stats-<?php echo $key; ?>"></span>
<? endforeach; ?>
 * @endcode
 *
 * Note: The examples above use SPAN tags, but you may use DIV, P, etc.
 *
 * @see https://github.com/openscholar/ossap
 */
function ossapRealtimeStatsRefresh() {
  var realtimeUrl = '/ossap/real-time-visitors';
  var updateInterval = 2000;
  var fetchInterval = 15000;
  var realtimeDivId = ".ossap-stats-real-time-visitors";
  var weeklyVisitorsDivId = ".ossap-stats-weekly-visitors";
  var monthlyVisitorsDivId = ".ossap-stats-monthly-visitors";
  var weeklyPageViewsDivId = ".ossap-stats-weekly-pv";
  var monthlyPageViewsDivId = ".ossap-stats-monthly-pv";

  setInterval(function(){
    fetchInterval = fetchInterval - updateInterval;
    if(fetchInterval < 0) {
      $.getJSON(realtimeUrl, function( data ) {
        $(realtimeDivId).text(data[0]);
        $(weeklyVisitorsDivId).text(data[1]).OSAddCommas();
        $(monthlyVisitorsDivId).text(data[2]).OSAddCommas();
        $(weeklyPageViewsDivId).text(data[3]).OSAddCommas();
        $(monthlyPageViewsDivId).text(data[4]).OSAddCommas();

        var d = new Date();
        var n = d.getHours() + 1;
        n = n + ((d.getMinutes() + 1) / 60);
        var Vinterval = Math.floor((parseInt($(weeklyVisitorsDivId).text().replace(/,/g, ''), 10) / 7 / 24) * n);
        var PVinterval = Math.floor((parseInt($(weeklyPageViewsDivId).text().replace(/,/g, ''), 10) / 7 / 24) * n);

        $(weeklyVisitorsDivId).text(parseInt($(weeklyVisitorsDivId).text().replace(/,/g, ''), 10) + Vinterval).OSAddCommas();
        $(monthlyVisitorsDivId).text(parseInt($(monthlyVisitorsDivId).text().replace(/,/g, ''), 10) + Vinterval).OSAddCommas();
        $(weeklyPageViewsDivId).text(parseInt($(weeklyPageViewsDivId).text().replace(/,/g, ''), 10) + PVinterval).OSAddCommas();
        $(monthlyPageViewsDivId).text(parseInt($(monthlyPageViewsDivId).text().replace(/,/g, ''), 10) + PVinterval).OSAddCommas();
      });
      fetchInterval = 15000;
    } else {
      // Add random -10 to 10 people.
      var plusOrMinus = Math.random() < 0.5 ? -5 : 5;
      $(realtimeDivId).text(parseInt($(realtimeDivId).text().replace(/,/g, ''), 10) + Math.floor(Math.random() * plusOrMinus)).OSAddCommas();

      var Vinterval = Math.floor(parseInt($(weeklyVisitorsDivId).text().replace(/,/g, ''), 10) / 7 / 24 / 60 / 30);
      var PVinterval = Math.floor(parseInt($(weeklyPageViewsDivId).text().replace(/,/g, ''), 10) / 7 / 24 / 60 / 30);

      $(weeklyVisitorsDivId).text(parseInt($(weeklyVisitorsDivId).text().replace(/,/g, ''), 10) + Vinterval).OSAddCommas();
      $(monthlyVisitorsDivId).text(parseInt($(monthlyVisitorsDivId).text().replace(/,/g, ''), 10) + Vinterval).OSAddCommas();
      $(weeklyPageViewsDivId).text(parseInt($(weeklyPageViewsDivId).text().replace(/,/g, ''), 10) + PVinterval).OSAddCommas();
      $(monthlyPageViewsDivId).text(parseInt($(monthlyPageViewsDivId).text().replace(/,/g, ''), 10) + PVinterval).OSAddCommas();
    }
  }, updateInterval);

}

(function(){

<? foreach ($aggregates as $key => $value): ?>
  // Current <?php echo $key; ?>

  var elements = document.getElementsByClassName('ossap-stats-<?php echo $key; ?>');
  for(var i = 0; i < elements.length; i++) {
    <?php $value = ($key == 'filesize_bytes') ? format_size($value) : $value ?>
    <?php $value = ($key != "os_version" && is_numeric($value)) ? number_format($value) : $value ?>
    <?php $value = ($key == 'activity-messages') ? $value : "'{$value}'" ?>
    elements[i].innerHTML = <?php echo $value; ?>;
  }

<? endforeach; ?>

$.fn.OSAddCommas = function(){
    return this.each(function(){
        $(this).text( $(this).text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") );
    })
}

ossapRealtimeStatsRefresh();
})();
