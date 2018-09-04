<?php
namespace App\Library\_Class {

    use Carbon\Carbon;
    use Log;

	class DocumentUtil {

        static public function genDocNumber($type)
        {
            $upperTypeName = strtoupper($type);
            $dateStr = Carbon::now()->format('YmdHi');
            $prefixNumber = $upperTypeName . '-' . $dateStr;
    
            $countSameType = \App\DocumentDetail::where('number', 'like', $prefixNumber . '%')->count();
    
            return $prefixNumber . (str_pad(($countSameType + 1), 3, '0', STR_PAD_LEFT));
        }

    }
}
