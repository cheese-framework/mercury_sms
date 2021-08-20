<?php

namespace App\Helper\Voucher;

use App\Auth\Auth;
use App\Database\Database;
use DateTime;
use Exception;

class Voucher
{

    public static function isVoucherValid($voucher)
    {
        $db = Database::getInstance();
        $db->query("SELECT isUsed FROM vouchers WHERE voucher=?");
        $db->bind(1, $voucher);
        $result = $db->single();
        if ($db->rowCount() > 0) {
            return $result->isUsed == 1 ? FALSE : TRUE;
        }
        return FALSE;
    }

    public static function activateWithVoucher($voucher, $schoolId, $schoolEmail)
    {
        $db = Database::getInstance();
        $db->query("UPDATE vouchers SET isUsed=1 WHERE voucher=?");
        $db->bind(1, $voucher);
        $db->execute();
        if ($db->rowCount() > 0) {
            $voucherData = self::getVoucher($voucher);
            if ($voucherData) {
                $startDate = date("Y-m-d H:i:s");
                $db->query("INSERT INTO billings (paymentId, frequency, currency,amount,cycles,billingInterval,status,payerEmail,username,payerId,startDate,endDate,duration) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $db->bind(1, substr(Auth::generateToken(), 0, 10));
                $db->bind(2, $voucherData->type);
                $db->bind(3, "DALASIS");
                $db->bind(4, $voucherData->amount);
                $db->bind(5, 1);
                $db->bind(6, 1);
                $db->bind(7, "Active");
                $db->bind(8, $schoolEmail);
                $db->bind(9, $schoolEmail);
                $db->bind(10, $schoolId);
                $db->bind(11, $startDate);
                $db->bind(12, $voucherData->enddate);
                $db->bind(13, $voucherData->days);
                $db->execute();
                if ($db->rowCount() > 0) {
                    $id = $db->lastInsertId();
                    $db->query("UPDATE school SET isActivated=1, payment=?, useSMS=?, usingFirstTimePromo=0 WHERE schoolId=?");
                    $db->bind(1, $id);
                    $db->bind(2, $voucherData->useSMS);
                    $db->bind(3, $schoolId);
                    $db->execute();
                    return $db->rowCount() > 0;
                }
                return FALSE;
            }
            return FALSE;
        }
        return FALSE;
    }

    public static function getVoucher($voucher)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM vouchers WHERE voucher=?");
        $db->bind(1, $voucher);
        $voucherData = $db->single();
        return ($db->rowCount() > 0) ? $voucherData : NULL;
    }

    public static function addVoucher($type, $date, $amount, $useSMS = FALSE)
    {
        $voucher = self::makeVoucher();
        $today = new DateTime(date("Y-m-d H:i:s"));
        $endDate = new DateTime($date);
        $diff = $today->diff($endDate);
        $days = $diff->days;
        if (!$days) throw new Exception("The voucher is expected to be at least a month's old");
        $db = Database::getInstance();
        $db->query("INSERT INTO vouchers (voucher, enddate, useSMS, type, days,amount) VALUES(?,?,?,?,?,?);");
        $db->bind(1, $voucher);
        $db->bind(2, $date);
        $db->bind(3, $useSMS);
        $db->bind(4, $type);
        $db->bind(5, $days);
        $db->bind(6, $amount);
        $db->execute();
        return $voucher;
    }

    private static function makeVoucher()
    {
        $voucher = self::generateVoucher();
        if (!self::getVoucher($voucher)) {
            return $voucher;
        } else {
            self::makeVoucher();
        }
    }

    private static function generateVoucher()
    {
        return strtoupper(substr(Auth::generateToken(), 0, 15));
    }

    public static function delete($voucher)
    {
        $db = Database::getInstance();
        $db->query("DELETE FROM vouchers WHERE voucher=?");
        $db->bind(1, $voucher);
        $db->execute();
    }
}
