<?php
namespace App\Library {

    use Illuminate\Support\Facades\Auth;

	class Token {

		static public function getToken() {
            $id = Auth::id();
            $user = \App\User::find($id);
            $token = $user->createToken('TokenAccessAPI')->accessToken;
            return $token;
		}

	}
}
