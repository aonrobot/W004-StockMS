<?php
namespace App\Library\Log {
    use Carbon\Carbon;
    use App\Inventory as InventoryModel;
    use App\InventoryLog;
    use Log;

	class Inventory {

        static private function logFile($invenId, $type, $amount, $remark, $date)
        {
            $paraStr = 'Inventory id: ' . $invenId . ' | ' . $type . ' | ' . $amount . ' | ' . $remark . ' | ' . $date;
            Log::channel('stockms')->info('Inventory Log : ' . $paraStr);
        }
        
        static public function write($invenId, $type, $amount, $remark = 'no comment', $date) 
        {
            // Check date isn't empty
            $date = ($date == null) ? Carbon::now()->toDateString() : $date;

            self::logFile($invenId, $type, $amount, $remark, $date);

            // Check Inventory id
            if(!InventoryModel::where('id', $invenId)->count()) return ['error' => 'Not found this inventory id'];

            // Find Log
            $invenLog = InventoryLog::where('inventory_id', $invenId)->where('log_date', $date)->where('type', $type);
            $invenLogCount = $invenLog->count();
            
            // Create time from current time
            $d = Carbon::now();
            $timeNow = $d->toTimeString();

            // Create New Record
            //if(!$invenLogCount) {
                $invenLogId = InventoryLog::create([
                    'inventory_id' => $invenId,
                    'type' => $type,
                    'amount' => $amount, 
                    'remark' => $remark,
                    'log_date' => $date,
                    'log_time' => $timeNow
                ])->id;
                
                // Response id
                return [
                    'created' => true,
                    'message' => 'create new log',
                    'id' => $invenLogId,
                    'amount' => $amount
                ];
            
            // Update only amount
            // } else {
            //     $lastAmount = $invenLog->first(['amount'])->amount;
            //     $newAmount = $lastAmount + $amount;
            //     $invenLog->update([
            //         'amount' => $newAmount,
            //         'log_time' => $timeNow
            //     ]);

            //     // Response amount
            //     return [
            //         'created' => true,
            //         'message' => 'update log id ' . $invenLog->first(['id'])->id,
            //         'amount' => $newAmount
            //     ];
            // }
		}

	}
}
