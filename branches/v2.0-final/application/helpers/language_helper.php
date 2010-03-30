<?php
// Useful edition functions

function language_name($code) {
  switch (strtolower($code)) {
    case 'eng': return "English";
    case 'ita': return "Italian";
    case 'spa': return "Spanish";
  }
  return $code;
}


