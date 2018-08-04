<?php
namespace App\Library {

    use Illuminate\Http\Response;
	class Model {

		static public function checkIntegrity() {
            Response::json(['error' => 'Not found this inventory id']);
            die();
		}

	}
}
