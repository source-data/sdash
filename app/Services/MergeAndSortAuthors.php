<?php

namespace App\Services;

class MergeAndSortAuthors
{

  static function mergeAndSort(array $registeredAuthors, array $externalAuthors): array
  {
    $allAuthors = array_filter(array_merge($registeredAuthors, $externalAuthors), function ($authorItem) {
      return $authorItem["author_role"]["role"] !== 'curator';
    });
    $sortedAuthors = [];
    for ($i = 0; $i < count($allAuthors); $i++) {
      $sortedAuthors[$allAuthors[$i]["author_role"]["order"]] = [
        "firstname" =>  $allAuthors[$i]["firstname"],
        "surname" =>  $allAuthors[$i]["surname"],
        "department_name" =>  $allAuthors[$i]["department_name"],
        "institution_name" =>  $allAuthors[$i]["institution_name"],
        "institution_address" =>  isset($allAuthors[$i]["institution_address"]) ? $allAuthors[$i]["institution_address"] : ''
      ];
    }
    ksort($sortedAuthors);
    return $sortedAuthors;
  }
}
