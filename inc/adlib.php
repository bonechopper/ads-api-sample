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

class AdTargetingEducation {
  const ALL = 0;
  const IN_HIGH_SCHOOL = 1;
  const IN_COLLEGE = 2;
  const COLLEGE_GRAD = 3;
}

class AdTargetingRelationship {
  const SINGLE = 1;
  const IN_RELATIONSHIP = 2;
  const ENGAGED = 3;
  const MARRIED = 4;
}

class AdTargetingSex {
  const MALE = 1;
  const FEMALE = 2;
}

class AdBidType {
  const CPC = 1;
  const CPM = 2;
}

class CampaignStatus {
  const ACTIVE = 1;
  const PAUSED = 2;
  const DELETED = 3;
}

class AdGroupStatus {
  const ACTIVE = 1;
  const CAMPAIGN_PAUSED = 2;
  const DELETED = 3;
  const PENDING_REVIEW = 4;
  const DISAPPROVED = 5;
  const PREAPPROVED = 6;
  const ADGROUP_PAUSED = 9;
}


function getDateString($date) {
  return date('F j, Y', $date);
}

function getStatusString($status) {
  switch ($status) {
  case AdGroupStatus::ACTIVE:
    return 'active';
    break;
  case AdGroupStatus::CAMPAIGN_PAUSED:
    return 'paused';
    break;
  case AdGroupStatus::ADGROUP_PAUSED:
    return 'paused';
    break;
  case AdGroupStatus::DELETED:
    return 'deleted';
    break;
  case AdGroupStatus::PENDING_REVIEW:
    return 'pending review';
    break;
  case AdGroupStatus::DISAPPROVED:
    return 'disapproved';
    break;
  case AdGroupStatus::PREAPPROVED:
    return 'preapproved';
    break;
  }
   return null;
}
 function getBidTypeString($bid_type) {
  if ($bid_type == 1) {
    return 'cpc';
  } else if ($bid_type == 2) {
    return 'cpm';
  }
   return null;
}

function getMoneyString($amount) {
  return number_format($amount/100, 2, '.', ',');
}

function array_pull($array, $key) {
  $result = array();
   foreach ($array as $row) {
    $result []= $row[$key];
  }
   return $result;
}
function getStat($stats, $id) {
  foreach ($stats as $stat) {
    $stat = $stat['stats'][$id];
    if ((isset($stat['id'])) && ($stat['id'] == $id)) {
      return $stat;
    }
  }
   return null;
}

function getLocales() {
  return array(
               "ca_ES" =>  1,
               "cs_CZ" =>  2,
               "cy_GB" =>  3,
               "da_DK" =>  4,
               "de_DE" =>  5,
               "en_US" =>  6,
               "es_ES" =>  7,
               "fi_FI" =>  8,
               "fr_FR" =>  9,
               "it_IT" =>  10,
               "ja_JP" =>  11,
               "ko_KR" =>  12,
               "nb_NO" =>  13,
               "nl_NL" =>  14,
               "pl_PL" =>  15,
               "pt_BR" =>  16,
               "ru_RU" =>  17,
               "sv_SE" =>  18,
               "tr_TR" =>  19,
               "zh_CN" =>  20,
               "zh_HK" =>  21,
               "zh_TW" =>  22,
               "es_LA" =>  23,
               "en_GB" =>  24,
               "id_ID" =>  25,
               "tl_PH" =>  26,
               "vi_VN" =>  27,
               "ar_AR" =>  28,
               "he_IL" =>  29,
               "hu_HU" =>  30,
               "pt_PT" =>  31,
               "ro_RO" =>  32,
               "sk_SK" =>  33,
               "sl_SI" =>  34,
               "th_TH" =>  35,
               "af_ZA" =>  36,
               "bg_BG" =>  37,
               "hr_HR" =>  38,
               "el_GR" =>  39,
               "lt_LT" =>  40,
               "ms_MY" =>  41,
               "sr_RS" =>  42,
               "en_PI" =>  43,
               "fr_CA" =>  44,
               );
}
