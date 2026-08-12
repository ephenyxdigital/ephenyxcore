<?php


namespace EphenyxDigital\EphenyxCore;

use ComposerMap;
class Mapper {

	public function init() {

		ComposerMap::setInit();

		require DIGITAL_CORE_DIR . '/vendor/map.php';

	}

}
