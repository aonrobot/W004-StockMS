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

        static private function writeCurrentQty($invenId){
            $quantity = intval(InventoryModel::where('id', $invenId)->first(['quantity'])->quantity);
            return $quantity;
        }

        static public function updateCurrentQty($date, $amount){

        }
        
        static public function write($invenId, $type, $amount, $date, $remark = 'no comment') 
        {
            // Check date isn't empty
            $date = ($date == null) ? Carbon::now()->toDateString() : $date;

            self::logFile($invenId, $type, $amount, $remark, $date);

            // Check Inventory id
            if(!InventoryModel::where('id', $invenId)->count()) return ['created' => false, 'message' => 'Not found this inventory id'];

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
                'quantity' => 0,
                'log_date' => $date,
                'log_time' => $timeNow
            ])->id;

            InventoryLog::where('id', $invenLogId)->update(['quantity' => self::writeCurrentQty($invenId)]);
            
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
