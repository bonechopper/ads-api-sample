<?php
/**
 * Copyright 2010 Facebook, Inc.
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

require './inc/includes.php';
$page_time_start = time();
print_header($facebook, $me, $session);

if(!$account_id) {
  echo "Please create an ads account first by going to:
  <a href='http://www.facebook.com/ads'>facebook.com/ads>
  </a>";
  exit();
}

echo '
  <form method=post action="index.php" id="form">
    <input type=hidden name="action" id="action">
    <input type=hidden name="adgroup_id" id="adgroup_id">
    <input type=hidden name="campaign_id" id="campaign_id">
    <input type=hidden name="max_bid" id="max_bid">
    <input type=hidden name="ad_name" id="ad_name">
    <input type=hidden name="status" id="status">
  </form>';

$today = strtotime('today');
$time_start = $today;
$time_stop = strtotime('+1 day', $today);

if (!empty($_POST["action"])) {
  $result = null;
  $success = false;
  switch ($_POST["action"]) {
    case 'pauseCampaign':
      $result = $facebook->api(array(
                  'method'         => 'ads.updateCampaigns',
                  'account_id'     => $account_id,
                  'campaign_specs' =>
                    array(
                      array(
                        'campaign_id'     => $_POST["campaign_id"],
                        'campaign_status' => CampaignStatus::PAUSED))));
      if (!empty($result['updated_campaigns'])) {
        $success = true;
      }
      break;
    case 'resumeCampaign':
      $result = $facebook->api(array(
                  'method'         => 'ads.updateCampaigns',
                  'account_id'     => $account_id,
                  'campaign_specs' =>
                    array(
                      array(
                        'campaign_id'     => $_POST["campaign_id"],
                        'campaign_status' => CampaignStatus::ACTIVE))));
      if (!empty($result['updated_campaigns'])) {
        $success = true;
      }
      break;
    case 'deleteCampaign':
      $result = $facebook->api(array(
                  'method'         => 'ads.updateCampaigns',
                  'account_id'     => $account_id,
                  'campaign_specs' =>
                    array(
                      array(
                        'campaign_id'     => $_POST["campaign_id"],
                        'campaign_status' => CampaignStatus::DELETED))));
      if (!empty($result['updated_campaigns'])) {
        $success = true;
      }
      break;
    case 'pauseAdGroup':
      $result = $facebook->api(array(
                  'method'        => 'ads.updateAdGroups',
                  'account_id'    => $account_id,
                  'adgroup_specs' =>
                    array(
                      array(
                        'adgroup_id' => $_POST["adgroup_id"],
                        'ad_status'  => AdGroupStatus::ADGROUP_PAUSED))));
      if (!empty($result['updated_adgroups'])) {
        $success = true;
      }
      break;
    case 'resumeAdGroup':
      $result = $facebook->api(array(
                  'method'        => 'ads.updateAdGroups',
                  'account_id'    => $account_id,
                  'adgroup_specs' =>
                    array(
                      array(
                        'adgroup_id' => $_POST["adgroup_id"],
                        'ad_status'  => AdGroupStatus::ACTIVE))));
      if (!empty($result['updated_adgroups'])) {
        $success = true;
      }
      break;
    case 'deleteAdGroup':
      $result = $facebook->api(array(
                  'method'        => 'ads.updateAdGroups',
                  'account_id'    => $account_id,
                  'adgroup_specs' =>
                    array(
                      array(
                        'adgroup_id' => $_POST["adgroup_id"],
                        'ad_status'  => AdGroupStatus::DELETED))));
      if (!empty($result['updated_adgroups'])) {
        $success = true;
      }
      break;
    case 'updateAdGroup':
      $spec = array('adgroup_id' => $_POST['adgroup_id'],
                    'name'       => $_POST['ad_name'],
                    'max_bid'    => $_POST['max_bid'] * 100);
      $specs = array($spec);
      $result = $facebook->api(
                  array(
                    'method'        => 'ads.updateAdGroups',
                    'account_id'    => $account_id,
                    'adgroup_specs' => $specs));
      if (!empty($result['updated_adgroups'])) {
        $success = true;
      }
      break;
  }

  if (!$success) {
    echo 'Operation failed.<br><pre>' . "\n";
    echo var_export($result, true);
    echo '</pre>';
  }
}

try {
  $campaigns = $facebook->api(array(
                 'method'          => 'ads.getCampaigns',
                 'account_id'      => $account_id,
                 'campaign_id'     => array(),
                 'include_deleted' => false));

  $all_adgroups = $facebook->api(array(
                    'method'          => 'ads.getAdGroups',
                    'account_id'      => $account_id,
                    'campaign_ids'    => array(),
                    'adgroup_ids'     => array(),
                    'include_deleted' => false));

  $adgroup_stats = $facebook->api(array(
                     'method'          => 'ads.getAdGroupStats',
                     'account_id'      => $account_id,
                     'campaign_ids'    => array(),
                     'adgroup_ids'     => array(),
                     'include_deleted' => false,
                     'time_ranges' =>
                       array(array('time_start' => $time_start,
                                   'time_stop' => $time_stop))));

  $all_targeting = $facebook->api(array(
                     'method' => 'ads.getAdGroupTargeting',
                     'account_id' => $account_id,
                     'campaign_ids' => array(),
                     'adgroup_ids' => array(),
                     'include_deleted' => false));

  $creatives = $facebook->api(array(
                 'method' => 'ads.getAdGroupCreatives',
                 'account_id' => $account_id,
                 'campaign_ids' => array(),
                 'adgroup_ids' => array(),
                 'include_deleted' => false));

  $campaign_stats = $facebook->api(array(
                      'method' => 'ads.getCampaignStats',
                      'account_id' => $account_id,
                      'campaign_ids' => array(),
                      'include_deleted' => false,
                      'time_ranges' =>
                        array(array('time_start' => $time_start,
                                    'time_stop' => $time_stop))));

} catch (Exception $e) {
  echo '<pre>Exception: ' . $e->getMessage() . var_export($e, true).'</pre>';
  echo '</body></html>';
  return;
}

echo '<div class="ads_manager">
<table>';
foreach ($campaigns as $campaign) {
  echo '<tr style="background-color:#aaa;">' .
        '<td>';
  if ($campaign['campaign_status'] == CampaignStatus::ACTIVE) {
    echo '<input type=button onclick="pauseCampaign(' . $campaign['campaign_id'] . ')" value="Pause">';
  } else if ($campaign['campaign_status'] == CampaignStatus::PAUSED) {
    echo '<input type=button onclick="resumeCampaign(' . $campaign['campaign_id'] . ')" value="Resume">';
  }
  echo '<input type=button onclick="deleteCampaign(' . $campaign['campaign_id'] . ')" value="Delete"></td>';
  echo '<td>Campaign ' . $campaign['campaign_id'] . '</td>' .
        '<td>Campaign Name: ' . $campaign['name'] . '</td>' .
        '<td>from ' . getDateString($campaign['time_start']) . ' ' .
        'to ' . ($campaign['time_stop'] ? getDateString($campaign['time_stop']) : '') . '</td>' .
        '<td>Daily Budget: ' . getMoneyString($campaign['daily_budget']) . '</td>' .
        '<td>Status: ' . getStatusString($campaign['campaign_status']) . '</td>';
  $stat = getStat($campaign_stats, $campaign['campaign_id']);
  if ($stat) {
    $impressions = 0;
    $clicks = 0;
    $spend = 0;
    if ($stat) {
      $impressions = $stat['impressions'];
      $clicks = $stat['clicks'];
      $spend = $stat['spent'];
    }
    echo '<td>Impressions: ' . $impressions . ', ' .
         'Clicks: ' . $clicks . ', ' .
         'Cost: ' . getMoneyString($spend) . '</td>';
  }
  echo '</tr>';

  $adgroups = array();
  foreach ($all_adgroups as $adgroup) {
    if ($adgroup['campaign_id'] == $campaign['campaign_id']) {
      $adgroups []= $adgroup;
    }
  }

  if (!$adgroups) {
    echo '<tr><td colspan=10>No ads</td></tr>';
  } else {
    echo '<tr><td colspan=10><table class="datakit_table"><tr>' .
          '<th></th>' .
          '<th>Ad ID</th>' .
          '<th>Name</th>' .
          '<th>Status</th>' .
          '<th>Max Bid ($)</th>' .
          '<th>Type</th>' .
          '<th>Targeting</th>' .
          '<th>Creative</th>' .
          '<th>Impressions</th>' .
          '<th>Clicks</th>' .
          '<th>Cost</th>' .
          '</tr>';

    foreach ($adgroups as $adgroup) {
      $adgroup_i++;
      $bid_type = getBidTypeString($adgroup['bid_type']);
      $stat = getStat($adgroup_stats, $adgroup['adgroup_id']);
      $impressions = 0;
      $clicks = 0;
      $spend = 0;
      if ($stat) {
        $impressions = $stat['impressions'];
        $clicks = $stat['clicks'];
        $spend = $stat['spent'];
      }

      echo '<tr><td>';
      if ($adgroup['adgroup_status'] == AdGroupStatus::ACTIVE) {
        echo '<input type=button onclick="pauseAdGroup(' . $adgroup['adgroup_id'] . ')" value="Pause">';
      } else if ($adgroup['adgroup_status'] == AdGroupStatus::ADGROUP_PAUSED) {
        echo '<input type=button onclick="resumeAdGroup(' . $adgroup['adgroup_id'] . ')" value="Resume">';
      }

      echo '<input type=button onclick="deleteAdGroup(' . $adgroup['adgroup_id'] . ')" value="Delete"></td>';
      echo '<td>' . $adgroup["adgroup_id"] . '</td>' .
            '<td>' . $adgroup['name'] . '</td>' .
            '<td>' . getStatusString($adgroup['ad_status']) . '</td>' .
            '<td>' . getMoneyString($adgroup['max_bid']) . '</td>' .
            '<td>' . $bid_type . '</td>' .
            '<td><pre>';

      if (isset($all_targeting[$adgroup['adgroup_id']])) {
        $targeting = $all_targeting[$adgroup['adgroup_id']];

        foreach ($targeting as $k => $v) {
          echo $k . ' ' . var_export($v, true) . "\n";
        }
      }

      echo '</pre></td>' .
            '<td width=100px>';

      if (isset($creatives[$adgroup['adgroup_id']])) {
        $creative = $creatives[$adgroup['adgroup_id']];

        echo '<a class="creative" href="' . $creative['link_url'] . '">' .
                '<b>' . $creative['title'] . '</b>' .
             '</a> ';
        if (!empty($creative['image_url'])) {
          echo '<img src="' . $creative['image_url'] . '">';
        }
        echo $creative['body'];
      }

      echo '</td>' .
           '<td>' . $impressions . '</td>' .
           '<td>' . $clicks . '</td>' .
           '<td>' . getMoneyString($spend) . '</td>' .
           '<td>' .
            'Ad Name: <input type=text id="ad_' . $adgroup['adgroup_id'] . '_name" value="' . $adgroup['name'] . '"><br>' .
            'Max Bid: <input type=text id="ad_' . $adgroup['adgroup_id'] . '_bid" value="' . getMoneyString($adgroup['max_bid']) . '">' .
            '<input type=button value="Update" onclick="updateAdGroup(' . $adgroup['adgroup_id'] . ')">' .
            '</td>' .
           '</tr>';
    }
    echo '</table></td></tr>';
  }
}
echo '</table></div>';

echo '<br><br>Number of campaigns: ' . count($campaigns);
echo '<br><br>Number of ad groups: ' . count($all_adgroups);
$render_time = time() - $page_time_start;
echo '<br><br>Page Render Time: '
     . ($render_time == 1 ? '1 second.' : $render_time . ' seconds.');
echo '</body></html>';
