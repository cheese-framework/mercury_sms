<?php

namespace App\I18n;

use App\Database\Database;
use PDO;

class IsoCodesDb implements IsoCodesInterface
{
    protected $isoTableName;
    protected $iso2FieldName;
    protected $connection;
    public function __construct(
        Database $connection,
        $isoTableName,
        $iso2FieldName
    ) {
        $this->connection = $connection;
        $this->isoTableName = $isoTableName;
        $this->iso2FieldName = $iso2FieldName;
    }

    public function getCurrencyCodeFromIso2CountryCode($iso2): IsoCodes
    {
        $sql = sprintf(
            'SELECT * FROM %s WHERE %s = ?',
            $this->isoTableName,
            $this->iso2FieldName
        );
        $stmt = $this->connection->dbh->prepare($sql);
        $stmt->execute([$iso2]);
        return new IsoCodes($stmt->fetch(PDO::FETCH_ASSOC));
    }
}
