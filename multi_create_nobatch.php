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

try {
  $campaigns =
    $facebook->api(array(
      'method'          => 'ads.getCampaigns',
      'account_id'      => $account_id,
      'campaign_ids'    => array(),
      'include_deleted' => false));
} catch (Exception $e) {
  $error_message = $e->getMessage();
  echo var_export($error_message, true);
  exit;
}

function convertListToArray($s, $sep = ',') {
  return array_unique(array_map('trim', split($sep, $s)));
}

function getIdNameArray($s, $sep = ',') {
  $array = convertListToArray($s, $sep);
  if (!$array) {
    return null;
  }

  $result = array();
  foreach ($array as $v) {
    if (is_numeric($v)) {
      $result[] = array('id' => $v);
    } else {
      $result[] = array('name' => $v);
    }
  }

  return $result;
}

$error_message = '';
$targeting_stats = '';

$allLocales = getLocales();

$title = 'Title';
$body = 'Body';
$link_url = 'http://www.YourUrl.com/';
$min_age = 18;
$max_age = '';
$bid_type = AdBidType::CPC;
$male = false;
$female = false;
$max_bid = 0.3;
$name = '';
$campaign_id = 0;
$education = AdTargetingEducation::ALL;
$relationship = array();
$interested_in = array();
$college_years = array();
$country = 'US';
$regions = '';
$cities = '';
$radius = '';
$keywords = '';
$college_networks = '';
$college_years = array();
$college_majors = '';
$work_networks = '';
$locales = array();
$image_file_name = array();

if (!empty($_POST['submit']) || !empty($_POST['submit2'])) {
  $files = $_FILES;
  for ($ii = 1; $ii <3; $ii++) {
    $title = $_POST['title_'.$ii];
    $body = $_POST['body_'.$ii];
    $link_url = $_POST['link_url_'.$ii];
    $min_age = $_POST['min_age_'.$ii];
    $max_age = $_POST['max_age_'.$ii];
    $bid_type = $_POST['bid_type_'.$ii];
    $male = (isset($_POST['male_'.$ii])) ? $_POST['male_'.$ii] : null ;
    $female = (isset($_POST['female_'.$ii])) ? $_POST['female_'.$ii] : null;
    $max_bid = $_POST['max_bid_'.$ii];
    $name = $_POST['name_'.$ii];
    $campaign_id = $_POST['campaign_id_'.$ii];
    $education = $_POST['education_'.$ii];
    $country = $_POST['country_'.$ii];
    $regions = $_POST['regions_'.$ii];
    $cities = $_POST['cities_'.$ii];
    $radius = $_POST['radius_'.$ii];
    $keywords = $_POST['keywords_'.$ii];
    $college_networks = $_POST['college_networks_'.$ii];
    $college_majors = $_POST['college_majors_'.$ii];
    $work_networks = $_POST['work_networks_'.$ii];
    $target_path = null;

    if (!empty($_FILES['image_'.$ii]['name'])) {
      $target_path = '/tmp/';
      $target_path = $target_path.basename($_FILES['image_'.$ii]['name']);
      move_uploaded_file($_FILES['image_'.$ii]['tmp_name'], $target_path);
      $files['image_'.$ii]['tmp_name'] = $target_path;
      $image_file_name[$ii] = $_FILES['image_'.$ii]['name'];
    }

    if (!empty($_POST['relationship_single_'.$ii])) {
      $relationship[] = AdTargetingRelationship::SINGLE;
    }
    if (!empty($_POST['relationship_in_relationship_'.$ii])) {
      $relationship[] = AdTargetingRelationship::IN_RELATIONSHIP;
    }
    if (!empty($_POST['relationship_engaged_'.$ii])) {
      $relationship[] = AdTargetingRelationship::ENGAGED;
    }
    if (!empty($_POST['relationship_married_'.$ii])) {
      $relationship[] = AdTargetingRelationship::MARRIED;
    }

   if (!empty($_POST['interested_in_male_'.$ii])) {
      $interested_in[] = AdTargetingSex::MALE;
    }
    if (!empty($_POST['interested_in_female_'.$ii])) {
      $interested_in[] = AdTargetingSex::FEMALE;
    }

    if (!empty($_POST['college_years_2009_'.$ii])) {
      $college_years[] = 2009;
    }
    if (!empty($_POST['college_years_2010_'.$ii])) {
      $college_years[] = 2010;
    }
    if (!empty($_POST['college_years_2011_'.$ii])) {
      $college_years[] = 2011;
    }
    if (!empty($_POST['college_years_2012_'.$ii])) {
      $college_years[] = 2012;
    }

    foreach ($allLocales as $localeCode => $localeId) {
      if (!empty($_POST['locale_' . $localeId.'_'.$ii])) {
        $locales[] = $localeId;
      }
    }

    $adgroup_spec = array(
      'campaign_id'         => $campaign_id,
      'name'                => $name,
      'bid_type'            => $bid_type,
      'max_bid'             => round($max_bid * 100),
      'targeting'           => array(
      'countries'           => array($country)),
      'creative'            => array(
      'title'               => $title,
      'body'                => $body,
      'link_url'            => $link_url),
    );

    $targeting = &$adgroup_spec['targeting'];
    if ($male || $female) {
      $targeting['genders'] = array();
      if ($male) {
        $targeting['genders'][] = AdTargetingSex::MALE;
      }
      if ($female) {
        $targeting['genders'][] = AdTargetingSex::FEMALE;
      }
    }
    if ($relationship) {
      $targeting['relationship_statuses'] = $relationship;
    }
    if ($min_age) {
      $targeting['age_min'] = $min_age;
    }
    if ($max_age) {
      $targeting['age_max'] = $max_age;
    }
    if ($education) {
      $targeting['education_statuses'] = array($education);
    }
    if ($interested_in) {
      $targeting['interested_in'] = $interested_in;
    }

    if ($regions) {
      $array = getIdNameArray($regions);
      if ($array) {
        $targeting['regions'] = $array;
      }
    }

    if ($cities) {
      $array = getIdNameArray($cities, $sep = ';');
      if ($array) {
        $targeting['cities'] = $array;
      }
    }

    if ($radius > 0) {
      $targeting['radius'] = $radius;
    }

    if ($keywords) {
      $array = convertListToArray($keywords);
      if ($array) {
        $targeting['keywords'] = $array;
      }
    }

    if ($college_networks) {
      $array = getIdNameArray($college_networks);
      if ($array) {
        $targeting['college_networks'] = $array;
      }
    }

    if ($college_years) {
      $targeting['college_years'] = $college_years;
    }

    if ($college_majors) {
      $array = convertListToArray($college_majors);
      if ($array) {
        $targeting['college_majors'] = $array;
      }
    }

    if ($work_networks) {
      $array = getIdNameArray($work_networks);
      if ($array) {
        $targeting['work_networks'] = $array;
      }
    }

    if ($locales) {
      $targeting['locales'] = $locales;
    }
    $adgroup_specs[] = $adgroup_spec;
  } //End for loop

  if ($_POST['submit']) {
    if ($image_file_name[1]) {
      $adgroup_specs[0]['creative']['image_file'] = $image_file_name[1];
    }
    if ($image_file_name[2]) {
      $adgroup_specs[1]['creative']['image_file'] = $image_file_name[2];
    }
    if (!$image_file_name[1] && !$image_file_name[2]) {
      $files = null;
    }

    try {
      $params = array(
                  'method'        => 'ads.createAdGroups',
                  'account_id'    => $account_id,
                  'adgroup_specs' => $adgroup_specs);
      if ($files) {
        foreach ($files as $file) {
          if(!empty($file['name'])) {
            $params[basename($file['name'])] = '@'.$file['tmp_name'];
          }
        }
      }
      $result = $facebook->api($params);
      if (!empty($result['updated_adgroups'])) {
        foreach ($files as $file) {
          if ($file['tmp_name']) {
            unlink($file['tmp_name']);
          }
        }
        echo '<html><META HTTP-EQUIV=REFRESH CONTENT="1; URL=index.php"></html>';
        return;
      } else {
        $error_message = var_export($result, true);
      }
    } catch (Exception $e) {
      $error_message = $e->getMessage();
    }
  } else if ($_POST['submit2']) {
    $api_call = 'ads.estimateTargetingStats';
    try {
      $currency_code = 'USD';
      $targeting_stats = $facebook->api(array(
                           'method'         => 'ads.estimateTargetingStats',
                           'account_id'     => $account_id,
                           'targeting_spec' => $targeting,
                           'currency'       => $currency_code));
    } catch (Exception $e) {
      $error_message = $e->getMessage();
    }
  }
}

print_header($facebook, $me, $session);

if ($error_message) {
  echo 'Operation failed<br><br>';
  if ($api_call) {
    echo 'API Call: <pre>' . $api_call . '</pre><br>';
  }
  echo 'Request: <pre>' . var_export($adgroup_spec, true) . '</pre><br>';
  if ($result) {
    echo 'Response: <pre>' . var_export($result, true) . '</pre>';
  }
  echo 'Error Message: <pre>' . $error_message . '</pre><br>';
  if ($files) {
    echo 'FILES: <pre>' . var_export($files, true) . '</pre><br>';
  }
} else if ($targeting_stats) {
  echo 'Targeting Stats: <pre>' . var_export($targeting_stats, true) . '</pre>';
}

echo '
<form action="multi_create_nobatch.php" method="post" enctype="multipart/form-data">
<table>
<tr><td colspan=3 style="background-color: #aaaaaa;"><h2>Creative_1</h2></td></tr>
<tr><td>Ad Title</td><td><input type=text name="title_1" value="' . $title . '"></td></tr>
<tr><td>Ad Body</td><td><textarea name="body_1" rows="4" cols="40" maxlength="256">' . $body . '</textarea></td></tr>
<tr><td>URL:</td><td><input type=text name="link_url_1" size="80" maxlength="1024" value="' . $link_url . '"></td></tr>
<tr><td>Image:</td><td><input type=file name="image_1"></td></tr>
<tr><td colspan=3 style="background-color: #aaaaaa;"><h2>Targeting</h2></td></tr>
<tr><td>Location (Country)</td><td>
  <input type=text name="country_1" value="' . $country . '">
  Example: "US", "CA", "GB", "AU"
</td></tr>
<tr><td>Location (Regions)</td><td>
  <input type=text name="regions_1" value="' . $regions . '">
  Example: "Wyoming, District of Columbia, Ontario" (Comma Separated. Case-sensitive.)
</td></tr>
<tr><td>Location (Cities)</td><td>
  <input type=text name="cities_1" value="' . $cities . '">
  Example: "Washington, DC; New York, NY; Paris, France" (Semicolon Separated. Cities in USA and Canada must include two-letter state/province code. Cities outside USA and Canada must include the country name.)
</td></tr>
<tr><td>Radius</td><td><input type=text name="radius_1" value="' . $radius . '"></td></tr>
<tr><td>Age</td><td><input type=text name="min_age_1" size="5" value="' . $min_age . '"> - <input type=text name="max_age_1" size="5" value="' . $max_age . '"></td></tr>
<tr><td>Sex</td><td>
  <input type=checkbox name="male_1"' . ($male ? ' checked' : '') . '> Male<br>
  <input type=checkbox name="female_1"' . ($female ? ' checked' : '') . '> Female
</td></tr>
<tr><td>Keywords</td><td>
  <input type=text name="keywords_1" value="' . $keywords . '"> Example: "Music, Movies" (Comma Separated)
</td></tr>
<tr><td>Education</td><td>
  <input type=radio name="education_1" value="' . AdTargetingEducation::ALL . '"' . ($education == AdTargetingEducation::ALL ? ' checked' : '') . '> All<br>
  <input type=radio name="education_1" value="' . AdTargetingEducation::COLLEGE_GRAD . '"' . ($education == AdTargetingEducation::COLLEGE_GRAD ? ' checked' : '') . '> College Grad<br>
  <input type=radio name="education_1" value="' . AdTargetingEducation::IN_COLLEGE . '"' . ($education == AdTargetingEducation::IN_COLLEGE ? ' checked' : '') . '> In College<br>
  <input type=radio name="education_1" value="' . AdTargetingEducation::IN_HIGH_SCHOOL . '"' . ($education == AdTargetingEducation::IN_HIGH_SCHOOL ? ' checked' : '') . '> In High School
</td></tr>
<tr><td>Colleges</td><td>
  <input type=text name="college_networks_1" value="' . $college_networks . '"> Example: "Waterloo, MIT" (Comma Separated)
</td></tr>
<tr><td>College Years (Graduating in):</td><td>
  <input type=checkbox name="college_years_2009_1" value="2009"' . (in_array(2009, $college_years) ? ' checked' : '') . '> 2009<br>
  <input type=checkbox name="college_years_2010_1" value="2010"' . (in_array(2010, $college_years) ? ' checked' : '') . '> 2010<br>
  <input type=checkbox name="college_years_2011_1" value="2011"' . (in_array(2011, $college_years) ? ' checked' : '') . '> 2011<br>
  <input type=checkbox name="college_years_2012_1" value="2012"' . (in_array(2012, $college_years) ? ' checked' : '') . '> 2012
</td></tr>
<tr><td>College Majors</td><td>
  <input type=text name="college_majors_1" value="' . $college_majors . '"> Example: "Computer Science, English" (Comma Separated)
</td></tr>
<tr><td>Relationship</td><td>
  <input type=checkbox name="relationship_single_1"' . (in_array(AdTargetingRelationship::SINGLE, $relationship)  ? ' checked' : '') . '> Single<br>
  <input type=checkbox name="relationship_in_relationship_1"' . (in_array(AdTargetingRelationship::IN_RELATIONSHIP, $relationship) ? ' checked' : '') . '> In Relationship<br>
  <input type=checkbox name="relationship_married_1"' . (in_array(AdTargetingRelationship::MARRIED, $relationship) ? ' checked' : '') . '> Married<br>
  <input type=checkbox name="relationship_engaged_1"' . (in_array(AdTargetingRelationship::ENGAGED, $relationship) ? ' checked' : '') . '> Engaged
</td></tr>
<tr><td>Interested In</td><td>
  <input type=checkbox name="interested_in_male_1"' . (in_array(AdTargetingSex::MALE, $interested_in) ? ' checked' : '') . '> Men<br>
  <input type=checkbox name="interested_in_female_1"' . (in_array(AdTargetingSex::FEMALE, $interested_in) ? ' checked' : '') . '> Women</td></tr>
<tr><td>Workplaces</td><td>
  <input type=text name="work_networks_1" value="' . $work_networks . '"> Example: "IBM, Microsoft, Intel" (Comma Separated)
</td></tr>
<tr><td>Languages</td><td>';

foreach ($allLocales as $localeCode => $localeId) {
  echo '<input type=checkbox name="locale_' . $localeId . '_1"' . (in_array($localeId, $locales) ? ' checked' : '') . '>' . $localeCode;
}
echo '
</td></tr>
<tr><td colspan=3 style="background-color: #aaaaaa;"><h2>Campaigns and Pricing</h2></td></tr>
<tr><td>Campaign:</td><td><select name="campaign_id_1">';
foreach ($campaigns as $campaign) {
  echo '<option value="' . $campaign['campaign_id'] . '"' . ($campaign_id == $campaign['campaign_id'] ? ' selected' : '') . '>' . $campaign['name'] . '</option>';
}
echo '</select></td></tr>
<tr><td>Ad Name:</td><td><input type=text name="name_1" value="' . $name . '"></td></tr>
<tr><td>Bid Type:</td><td><input type=radio name="bid_type_1" value="' . AdBidType::CPC . '"' . ($bid_type == AdBidType::CPC ? ' checked' : '') . '>CPC<br>
                          <input type=radio name="bid_type_1" value="' . AdBidType::CPM . '"' . ($bid_type == AdBidType::CPM ? ' checked' : '') . '>CPM</td></tr>
<tr><td>Max Bid:</td><td><input type=text name="max_bid_1" value="' . $max_bid . '"></td></tr>
</table>
<table>
<tr><td colspan=3 style="background-color: #aaaaaa;"><h2>Creative_2</h2></td></tr>
<tr><td>Ad Title</td><td><input type=text name="title_2" value="' . $title . '"></td></tr>
<tr><td>Ad Body</td><td><textarea name="body_2" rows="4" cols="40" maxlength="256">' . $body . '</textarea></td></tr>
<tr><td>URL:</td><td><input type=text name="link_url_2" size="80" maxlength="1024" value="' . $link_url . '"></td></tr>
<tr><td>Image:</td><td><input type=file name="image_2"></td></tr>
<tr><td colspan=3 style="background-color: #aaaaaa;"><h2>Targeting</h2></td></tr>
<tr><td>Location (Country)</td><td>
  <input type=text name="country_2" value="' . $country . '">
  Example: "US", "CA", "GB", "AU"
</td></tr>
<tr><td>Location (Regions)</td><td>
  <input type=text name="regions_2" value="' . $regions . '">
  Example: "Wyoming, District of Columbia, Ontario" (Comma Separated. Case-sensitive.)
</td></tr>
<tr><td>Location (Cities)</td><td>
  <input type=text name="cities_2" value="' . $cities . '">
  Example: "Washington, DC; New York, NY; Paris, France" (Semicolon Separated. Cities in USA and Canada must include two-letter state/province code. Cities outside USA and Canada must include the country name.)
</td></tr>
<tr><td>Radius</td><td><input type=text name="radius_2" value="' . $radius . '"></td></tr>
<tr><td>Age</td><td><input type=text name="min_age_2" size="5" value="' . $min_age . '"> - <input type=text name="max_age_2" size="5" value="' . $max_age . '"></td></tr>
<tr><td>Sex</td><td>
  <input type=checkbox name="male_2"' . ($male ? ' checked' : '') . '> Male<br>
  <input type=checkbox name="female_2"' . ($female ? ' checked' : '') . '> Female
</td></tr>
<tr><td>Keywords</td><td>
  <input type=text name="keywords_2" value="' . $keywords . '"> Example: "Music, Movies" (Comma Separated)
</td></tr>
<tr><td>Education</td><td>
  <input type=radio name="education_2" value="' . AdTargetingEducation::ALL . '"' . ($education == AdTargetingEducation::ALL ? ' checked' : '') . '> All<br>
  <input type=radio name="education_2" value="' . AdTargetingEducation::COLLEGE_GRAD . '"' . ($education == AdTargetingEducation::COLLEGE_GRAD ? ' checked' : '') . '> College Grad<br>
  <input type=radio name="education_2" value="' . AdTargetingEducation::IN_COLLEGE . '"' . ($education == AdTargetingEducation::IN_COLLEGE ? ' checked' : '') . '> In College<br>
  <input type=radio name="education_2" value="' . AdTargetingEducation::IN_HIGH_SCHOOL . '"' . ($education == AdTargetingEducation::IN_HIGH_SCHOOL ? ' checked' : '') . '> In High School
</td></tr>
<tr><td>Colleges</td><td>
  <input type=text name="college_networks_2" value="' . $college_networks . '"> Example: "Waterloo, MIT" (Comma Separated)
</td></tr>
<tr><td>College Years (Graduating in):</td><td>
  <input type=checkbox name="college_years_2009_2" value="2009"' . (in_array(2009, $college_years) ? ' checked' : '') . '> 2009<br>
  <input type=checkbox name="college_years_2010_2" value="2010"' . (in_array(2010, $college_years) ? ' checked' : '') . '> 2010<br>
  <input type=checkbox name="college_years_2011_2" value="2011"' . (in_array(2011, $college_years) ? ' checked' : '') . '> 2011<br>
  <input type=checkbox name="college_years_2012_2" value="2012"' . (in_array(2012, $college_years) ? ' checked' : '') . '> 2012
</td></tr>
<tr><td>College Majors</td><td>
  <input type=text name="college_majors_2" value="' . $college_majors . '"> Example: "Computer Science, English" (Comma Separated)
</td></tr>
<tr><td>Relationship</td><td>
  <input type=checkbox name="relationship_single_2"' . (in_array(AdTargetingRelationship::SINGLE, $relationship)  ? ' checked' : '') . '> Single<br>
  <input type=checkbox name="relationship_in_relationship_2"' . (in_array(AdTargetingRelationship::IN_RELATIONSHIP, $relationship) ? ' checked' : '') . '> In Relationship<br>
  <input type=checkbox name="relationship_married_2"' . (in_array(AdTargetingRelationship::MARRIED, $relationship) ? ' checked' : '') . '> Married<br>
  <input type=checkbox name="relationship_engaged_2"' . (in_array(AdTargetingRelationship::ENGAGED, $relationship) ? ' checked' : '') . '> Engaged
</td></tr>
<tr><td>Interested In</td><td>
  <input type=checkbox name="interested_in_male_2"' . (in_array(AdTargetingSex::MALE, $interested_in) ? ' checked' : '') . '> Men<br>
  <input type=checkbox name="interested_in_female_2"' . (in_array(AdTargetingSex::FEMALE, $interested_in) ? ' checked' : '') . '> Women</td></tr>
<tr><td>Workplaces</td><td>
  <input type=text name="work_networks_2" value="' . $work_networks . '"> Example: "IBM, Microsoft, Intel" (Comma Separated)
</td></tr>
<tr><td>Languages</td><td>';

foreach ($allLocales as $localeCode => $localeId) {
  echo '<input type=checkbox name="locale_' . $localeId . '_2"' . (in_array($localeId, $locales) ? ' checked' : '') . '>' . $localeCode;
}
echo '
</td></tr>
<tr><td colspan=3 style="background-color: #aaaaaa;"><h2>Campaigns and Pricing</h2></td></tr>
<tr><td>Campaign:</td><td><select name="campaign_id_2">';
foreach ($campaigns as $campaign) {
  echo '<option value="' . $campaign['campaign_id'] . '"' . ($campaign_id == $campaign['campaign_id'] ? ' selected' : '') . '>' . $campaign['name'] . '</option>';
}
echo '</select></td></tr>
<tr><td>Ad Name:</td><td><input type=text name="name_2" value="' . $name . '"></td></tr>
<tr><td>Bid Type:</td><td><input type=radio name="bid_type_2" value="' . AdBidType::CPC . '"' . ($bid_type == AdBidType::CPC ? ' checked' : '') . '>CPC<br>
                          <input type=radio name="bid_type_2" value="' . AdBidType::CPM . '"' . ($bid_type == AdBidType::CPM ? ' checked' : '') . '>CPM</td></tr>
<tr><td>Max Bid:</td><td><input type=text name="max_bid_2" value="' . $max_bid . '"></td></tr>

<tr><td colspan=2><input type=submit name="submit" value="Save">  <input type=submit name="submit2" value="Get Targeting Stats"></td></tr>

</table>
</form>';

echo '</body></html>';
