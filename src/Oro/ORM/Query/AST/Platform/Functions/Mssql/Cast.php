<?php

namespace Oro\ORM\Query\AST\Platform\Functions\Mssql;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\SqlWalker;
use Oro\ORM\Query\AST\Functions\Cast as DqlFunction;
use Oro\ORM\Query\AST\Functions\SimpleFunction;
use Oro\ORM\Query\AST\Platform\Functions\PlatformFunctionNode;

class Cast extends PlatformFunctionNode
{
    /**
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        /** @var Node $value */
        $value = $this->parameters[DqlFunction::PARAMETER_KEY];
        $type = strtolower($this->parameters[DqlFunction::TYPE_KEY]);
        
        switch ($type) {
            case 'datetime':
                $timestampFunction = new Timestamp(
                    array(SimpleFunction::PARAMETER_KEY => $value)
                );

                return $timestampFunction->getSql($sqlWalker);
            case 'json':
                if (!$sqlWalker->getConnection()->getDatabasePlatform()->hasNativeJsonType()) {
                    $type = 'text';
                }
                break;
            case 'bool':
                $type = 'bit';
                break;
            case 'string':
                $type = 'nvarchar';
                break;
        }

        return 'CAST(' . $this->getExpressionValue($value, $sqlWalker) . ' AS ' . $type . ')';
    }
}
