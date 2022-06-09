<?php

namespace equilibrium\data;

use Propel\Generator\Model\Column;
use Propel\Generator\Model\Database;
use Propel\Generator\Model\ForeignKey;
use Propel\Generator\Model\Index;
use Propel\Generator\Model\Table;
use Propel\Generator\Model\Unique;
use Propel\Generator\Model\PropelTypes;
use Propel\Generator\Model\ColumnDefaultValue;
use Propel\Generator\Reverse\MysqlSchemaParser;

class CustomMysqlSchemaParser extends MysqlSchemaParser
{
	//Modified method in order to reverse views also
	protected function parseTables(Database $database, ?Table $filterTable = null) : void
	{
		$sql = 'SHOW FULL TABLES';

		if ($filterTable) {
			if ($schema = $filterTable->getSchema()) {
				$sql .= ' FROM ' . $database->getPlatform()->doQuoting($schema);
			}
			$sql .= sprintf(" LIKE '%s'", $filterTable->getCommonName());
		} else if ($schema = $database->getSchema()) {
			$sql .= ' FROM ' . $database->getPlatform()->doQuoting($schema);
		}

		$dataFetcher = $this->dbh->query($sql);

		// First load the tables (important that this happen before filling out details of tables)
		$tables = [];
		foreach ($dataFetcher as $row) {
			$name = $row[0];
			$type = $row[1];

			//This is the potato!
			if ($name == $this->getMigrationTable() || !in_array($type, array("BASE TABLE", "VIEW"))) {
				continue;
			}

			$table = new Table($name);
			$table->setIdMethod($database->getDefaultIdMethod());
			if ($filterTable && $filterTable->getSchema()) {
				$table->setSchema($filterTable->getSchema());
			}
			$database->addTable($table);
			$tables[] = $table;
		}
	}
}
